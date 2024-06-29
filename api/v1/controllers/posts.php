<?php

use BumpCore\EditorPhp\Block\Block;
use BumpCore\EditorPhp\Blocks\Delimiter;
use BumpCore\EditorPhp\Blocks\Embed;
use BumpCore\EditorPhp\Blocks\Image;
use BumpCore\EditorPhp\EditorPhp;
use BumpCore\EditorPhp\Helpers;

require_once MODELS . "users.php";
require_once MODELS . "contents.php";

class CustomYoutubeEmbed extends Embed
{
    public function render(): string
    {
        debug(var_export($this->data, true), __FILE__);
        return Helpers::renderNative(VIEWS . 'editor-youtube-embed.php', ["height" => $this->data->get('height'), "src" => $this->data->get('embed')]);
    }
}

class CustomImageGallery extends Block
{
    public function rules(): array
    {
        return [
            "urls" => "array"
        ];
    }

    public function render(): string
    {
        return Helpers::renderNative(VIEWS . 'editor-img-gallery.php', ["imgs" => $this->data->get('urls')]);
    }
}

class CustomDelimiter extends Delimiter
{
    public function render(): string
    {
        return "<hr />";
    }
}

class CustomSimpleImage extends Image
{
    public function rules(): array
    {
        return [
            "url" => "string",
            "caption" => "string"
        ];
    }

    public function render(): string
    {
        $data = $this->data;
        return Helpers::renderNative(VIEWS . 'editor-img.php', ["url" => $data('url')]);
    }
}

function create_blog()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        exit(1);
    }

    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $path = '/blogs/entry?p=' . ContentsModel::generate_slug($_POST['title']);
    $data = $_POST["data"];
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);
    $meta = json_encode([
            'title' => $_POST['title'],
            'cover' => $_POST['cover'],
            'tags' => $_POST['tags']]
    );

    try {
        $ok = $contents->write_content($path, $uid, $user_id, $compressed_data, $meta);

        if (!$ok) {
            // TODO: specify why
            throw new Exception("content update failed");
        }
    } catch (Exception $ex) {
        session_write_close();
        debug($ex->getMessage(), __FILE__);
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(201);
    echo $path;
    exit(0);
}

function save_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        exit(1);
    }

    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $path = $_POST["path"];
    $data = $_POST["data"];
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);

    try {
        $ok = $contents->update_content($path, $uid, $user_id, $compressed_data);

        if ($ok != 0) {
            // TODO: specify why
            throw new Exception("content update failed");
        }
    } catch (Exception $ex) {
        session_write_close();
        debug($ex->getMessage(), __FILE__);
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(204);
    exit(0);
}

function read_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        exit(1);
    }

    session_start();
    if (!isset($_SESSION["email"])) {
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        http_response_code(401);
        exit(1);
    }

    $contents = new ContentsModel();
    $path = $_GET["path"];

    $content = $contents->read_content($path);
    debug("request for the contents of path: $path ", __FILE__);

    $data = bzdecompress($content["data"]);

    header('Content-Type: application/json; charset=utf-8');
    echo $data;

    session_write_close();
    exit(0);
}

function read_post_html()
{
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }

    if (isset($_SERVER['HTTP_HX_CURRENT_URL'])) {
        $url = $_SERVER['HTTP_HX_CURRENT_URL'];
        $path = parse_url($url, PHP_URL_PATH);
        debug("path from HX-Header with url: $url and path: $path", __FILE__);
    } elseif (isset($_GET["path"])) {
        $path = $_GET["path"];
        debug("path from _GET with path: $path", __FILE__);
    } else {
        echo "NOT FOUND";
        http_response_code(404);
        exit(1);
    }

    $contents = new ContentsModel();
    $content = $contents->read_content($path);
    if ($content === false) {
        debug("content not found" . var_export($content, true), __FILE__);
        echo Helpers::renderNative(VIEWS . '404.html', []);
        exit(1);
    }
    $json = bzdecompress($content["data"]);

    EditorPhp::register([
        "imageGallery" => CustomImageGallery::class,
        "image" => CustomSimpleImage::class,
        "delimiter" => CustomDelimiter::class,
        "embed" => CustomYoutubeEmbed::class,
    ]);
    $render = EditorPhp::make($json)->render();

    echo $render;
    http_response_code(200);
    exit(0);
}

function create_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }


    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $path = $_POST["path"];
    $data = $_POST["data"];
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);

    try {
        $contents->write_content($path, $uid, $user_id, $compressed_data);
    } catch (Exception $ex) {
        session_write_close();
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(201);
    exit(0);
}

function save_blog()
{

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }


    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $title = $_POST["title"];
    $tags = $_POST["tags"];
    $cover = $_POST["cover"];
    $data = $_POST["data"];
    $path = '/blogs/entry?p=' . $contents->generate_slug($title) . '-' . (string)time();
    $meta = [
        'title' => $title,
        'tags' => $tags,
        'cover' => $cover
    ];
    $meta = json_encode($meta);
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);

    try {
        $contents->write_content($path, $uid, $user_id, $compressed_data, $meta);

    } catch (Exception $ex) {
        session_write_close();
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(201);
    echo $path;
    exit(0);
}

function read_blog_html()
{
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }

    if (isset($_GET["path"])) {
        $path = $_GET["path"];
        debug("path from _GET with path: $path", __FILE__);
    } else {
        debug(var_export($_GET, true), __FILE__);
        echo "NOT FOUND";
        http_response_code(404);
        exit(1);
    }

    $contents = new ContentsModel();
    $content = $contents->read_content($path);
    if ($content === false) {
        debug("content not found" . var_export($content, true), __FILE__);
        echo Helpers::renderNative(VIEWS . '404.html', []);
        http_response_code(404);
        exit(1);
    }
    $json = bzdecompress($content["data"]);

    EditorPhp::register([
        "imageGallery" => CustomImageGallery::class,
        "image" => CustomSimpleImage::class,
        "delimiter" => CustomDelimiter::class,
        "embed" => CustomYoutubeEmbed::class,
    ]);
    $render = EditorPhp::make($json)->render();

    $meta = $content['meta'];
    $meta = json_decode($meta, true);
    $tags = explode(',', $meta['tags']);

    echo Helpers::renderNative(VIEWS . 'skeleton-entry.php', [
        'path'=>$path,
        'date' => $content['updated_at'],
        'uid' => $content['uid'],
        'title' => $meta['title'],
        'blog' => $render,
        'tags' => $tags,
        'cover' => $meta['cover']
    ]);
    http_response_code(200);
    exit(0);
}

function available_contents()
{
    $c = new ContentsModel();
    $cs = $c->get_contents();
//    debug(var_export($cs, true), __FILE__)
    ?>
    <label class="lbl" for="path">Select a path to edit the content:</label>
    <select id="path" class="" style="color:black">
        <?php foreach ($cs as $i): ?>
            <option class="" value="<?= $i['path'] ?>"><?= $i['path'] ?></option>
        <?php endforeach; ?>
    </select>
<?php }

function read_blog_feat()
{
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }

    $c = new ContentsModel();
    $feat = $c->get_feat();

    echo Helpers::renderNative(VIEWS . 'blog-feat.php', [
        'cover' => $feat['meta']['cover'],
        'title' => $feat['meta']['title'],
        'date' => $feat['updated_at'],
        'path' => $feat['path']
    ]);
    exit(0);
}

function read_blog_list()
{
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }

    $c = new ContentsModel();
    $blogs = $c->get_blogs();

}
