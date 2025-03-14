<?php /** @noinspection PhpUndefinedVariableInspection */ ?>
<?php foreach ($projects as $project): ?>
    <tr class="bg-spc-bg-mid text-spc-light border-b-spc-dark border-b">
        <th scope="row" class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white">
            <?= $project['title'] ?>
        </th>
        <td class="px-6 py-4">
            <?= strlen($project['description']) > 45 ? substr($project['description'], 0, 45) . '...' : $project['description'] ?>
        </td>
        <td class="px-6 py-4">
            <?= $project['deadline'] ?>
        </td>
        <td class="px-6 py-4">
            <?= $project['status'] ?>
        </td>
        <td class="px-6 py-4">
            <div class="flex justify-start space-x-3">
                <a href="/projects/admin/edit?pid=<?= $project['id'] ?>"
                   class="px-4 py-2 rounded-sm text-white bg-spc-dark">Edit</a>
            </div>
        </td>
    </tr>
<?php endforeach; ?>
