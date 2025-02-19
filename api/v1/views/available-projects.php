<?php foreach ($cs as $c): ?>
    <tr class="bg-spc-bg-mid text-spc-light border-b-spc-dark border-b">
        <th scope="row" class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white">
            <?= $c['title'] ?>
        </th>
        <td class="px-6 py-4">
            <?= strlen($c['description']) > 45 ? substr($c['description'], 0, 45). '...' : $c['description'] ?>
        </td>
        <td class="px-6 py-4">
            <?= $c['deadline'] ?>
        </td>
        <td class="px-6 py-4">
            <?= $c['status'] ?>
        </td>
        <td class="px-6 py-4">
            <div class="flex justify-start space-x-3">
                <a href="/projects/admin/edit?path=<?= $c['id'] ?>"
                   class="px-4 py-2 rounded-sm text-white bg-spc-dark">Edit</a>
                <a href="#" class="px-4 py-2 rounded-sm text-white bg-spc-dark cursor-not-allowed">Delete</a>
            </div>
        </td>
    </tr>
<?php endforeach; ?>