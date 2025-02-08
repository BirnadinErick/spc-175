<?php foreach ($cs as $c): ?>
    <tr class="bg-spc-bg-mid text-spc-light border-b-spc-dark border-b">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            <?= $c['title'] ?>
        </th>
        <td class="px-6 py-4">
            <?= $c['time'] ?>
        </td>
        <td class="px-6 py-4">
            <div class="flex justify-start space-x-3">
                <a href="/author/edit-blog?slug=<?= $c['slug'] ?>"
                   class="px-4 py-2 rounded-sm text-white bg-spc-dark">Edit</a>
                <button hx-post="<?= API . 'delete-blog&slug='. $c['slug']  ?>" class="htmxenable px-4 py-2 rounded-sm text-white bg-spc-dark">Delete</button>
            </div>
        </td>
    </tr>
<?php endforeach; ?>