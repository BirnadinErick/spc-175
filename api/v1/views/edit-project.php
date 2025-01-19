<div class="mb-4">
    <label class="block mb-1" for="project_title">Project Title</label>
    <div
        class="bg-gradient-to-b w-full from-[#5ABF64] to-spc-gold p-[1px]"
    >
        <input
            class="text-white bg-spc-dark w-full focus:ring-0"
            type="text"
            name=project_title
            id=project_title
            spellcheck="true"
            required
            value="<?= $proj['title'] ?>"
        />
    </div>
</div>

    <div class="mb-8">
        <label class="block mb-1" for="desc">Description</label>
        <div
            class="bg-gradient-to-b w-full from-[#5ABF64] to-spc-gold p-[1px]"
>
                    <textarea
                        class="text-white bg-spc-dark w-full focus:ring-0 focus:outline-0"
                        name="desc"
                        id="desc"><?= $proj['description']?></textarea>
        </div>
    </div>

<div class="mb-4">
    <label class="block mb-1" for="deadline">Estimated Project Deadline</label>
    <div
        class="bg-gradient-to-b w-full from-[#5ABF64] to-spc-gold p-[1px]"
    >
        <input
            class="text-white bg-spc-dark w-full focus:ring-0"
            type="date"
            name=deadline
            id=deadline
            spellcheck="true"
            required
            value="<?= $proj['deadline'] ?>"
        />
    </div>
</div>

<div class="mb-4">
    <label class="block mb-1" for="amount">Projected Money Value in EUR</label>
    <div
        class="bg-gradient-to-b w-full from-[#5ABF64] to-spc-gold p-[1px]"
    >
        <input
            class="text-white bg-spc-dark w-full focus:ring-0"
            type="number"
            name=amount
            id=amount
            spellcheck="true"
            required
            value="<?= $proj['amount'] ?>"
        />
    </div>
</div>

<div class="mb-4">
    <label class="block mb-1" for="status">Project Status</label>
    <div class="bg-gradient-to-b w-full from-[#5ABF64] to-spc-gold p-[1px]">
        <select
            class="text-white bg-spc-dark w-full focus:ring-0"
            name="status"
            id="status"
            required
        >
            <?php
            // sync with global variable later: TODO
            $statusOptions = ['Active', 'Pending', 'Completed', 'Denied'];
            foreach ($statusOptions as $status):
                ?>
                <option
                    value="<?= htmlspecialchars($status) ?>"
                    <?= $proj['status'] === $status ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($status) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

    <div class="flex justify-end space-x-6 items-center">
        <button
            class="font-bold px-4 py-2 bg-white text-spc-green"
            type="submit">Save changes. </button >
    </div>
