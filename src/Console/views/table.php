<?php declare(strict_types=1);

/** @var string $titleHeader */
/** @var string $countHeader */
/** @var int $includeRelative */
/** @var array<array{0?: string, 1?: int|float|string, 2?: float|string|null, 3?: bool}> $rows */
?>
<div class="mt-1 mx-2 max-w-70">
    <div class="flex justify-between">
        <span class="text-green font-bold">
            <?php echo $titleHeader; ?>
        </span>
        <span class="lowercase">
            <?php echo $countHeader; ?>
            <?php if ($includeRelative !== 0) { ?>
                <span class="text-gray mr-1">/</span>
                <span class="text-gray font-bold">Relative</span>
            <?php }
            ?>
        </span>
    </div>
    <?php foreach ($rows as $row) { ?>
        <?php if ($row === []) { ?>
            <div />
        <?php } else { ?>
            <div class="flex space-x-1">
                <span class="<?php echo isset($row[3]) ? 'ml-1' : '' ?>">
                    <?php echo $row[0] ?>
                </span>
                <span class="flex-1 content-repeat-[.] text-gray" />
                <span><?php echo $row[1] ?></span>
                <?php if (isset($row[2])) { ?>
                    <span>
                        <span class="text-gray mr-1">/</span>
                        <span class="text-gray w-6 text-right"><?php echo $row[2] ?></span>
                    </span>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
