<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $label */
/** @var ?bool $includeRelative */
/** @var array<array{}|array{name: string, count: int|float|string, percent: float|string|null, isChild: bool}> $rows */
?>
<div class="mt-1 mx-2 max-w-70">
    <div class="flex justify-between">
        <span class="text-green font-bold">
            <?php echo $title; ?>
        </span>
        <span class="lowercase">
            <?php echo $label; ?>
            <?php if ($includeRelative ?? false) { ?>
                <span class="text-gray mr-1">/</span>
                <span class="text-gray font-bold">Relative</span>
            <?php } ?>
        </span>
    </div>
    <?php foreach ($rows as $row) { ?>
        <?php if ($row === []) { ?>
            <div />
        <?php } else { ?>
            <div class="flex space-x-1">
                <span class="<?php echo $row['isChild'] ? 'ml-1' : '' ?>">
                    <?php echo $row['name']; ?>
                </span>
                <span class="flex-1 content-repeat-[.] text-gray" />
                <span><?php echo $row['count']; ?></span>
                <?php if ($row['percent']) { ?>
                    <span>
                        <span class="text-gray mr-1">/</span>
                        <span class="text-gray w-6 text-right">
                            <?php echo $row['percent']; ?>
                        </span>
                    </span>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
