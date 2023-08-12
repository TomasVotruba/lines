<?php

declare(strict_types=1);

/** @var array<string, string> $all */
/** @var array<string, string> $lines */
?>
<div class="my-1 mx-2 max-w-150">
    <div class="flex justify-between">
        <span class="text-green font-bold">
            Metric
        </span>
        <span class="lowercase space-x-1">
            <span>All dependencies</span>
            <span class="text-gray">/</span>
            <span class="text-yellow">Without dev</span>
            <span class="text-gray">/</span>
            <span class="text-gray font-bold">Change</span>
        </span>
    </div>
    <div class="flex space-x-1">
        <span>All lines</span>
        <span class="content-repeat-[.] flex-1 text-gray" />
        <span class="lowercase space-x-1">
            <span>
                <?php echo $all['full']; ?>
            </span>
            <span class="text-gray">/</span>
            <span class="text-yellow">
                <?php echo $all['noDev']; ?>
            </span>
            <span class="text-gray">/</span>
            <span class="text-gray font-bold">
                <?php echo $all['percent']; ?>
            </span>
        </span>
    </div>
    <div class="flex space-x-1">
        <span>Lines of code</span>
        <span class="content-repeat-[.] flex-1 text-gray" />
        <span class="lowercase space-x-1">
            <span>
                <?php echo $lines['full']; ?>
            </span>
            <span class="text-gray">/</span>
            <span class="text-yellow">
                <?php echo $lines['noDev']; ?>
            </span>
            <span class="text-gray">/</span>
            <span class="text-gray font-bold">
                <?php echo $lines['percent']; ?>
            </span>
        </span>
    </div>
</div>
