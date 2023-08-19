<?php

declare(strict_types=1);

/** @var \TomasVotruba\Lines\ValueObject\TableView $tableView */

?>
<div class="mt-1 mx-2 max-w-60">
    <div class="flex justify-between">
        <span class="text-green font-bold">
            <?php echo $tableView->getTitle(); ?>
        </span>
        <span class="lowercase">
            <?php echo $tableView->getLabel(); ?>
            <?php if ($tableView->isShouldIncludeRelative()) { ?>
                <span class="text-gray mr-1">/</span>
                <span class="text-gray font-bold">Relative</span>
            <?php }
            ?>
        </span>
    </div>
    <?php foreach ($tableView->getRows() as $tableRow) { ?>
            <div class="flex space-x-1">
                <span class="<?php echo $tableRow->isChild() ? 'ml-1' : '' ?>">
                    <?php echo $tableRow->getName(); ?>
                </span>
                <span class="flex-1 content-repeat-[.] text-gray" />
                <span><?php echo $tableRow->getCount(); ?></span>
                <?php if ($tableRow->getPercent()) { ?>
                    <span>
                        <span class="text-gray mr-1">/</span>
                        <span class="text-gray w-6 text-right">
                            <?php echo $tableRow->getPercent(); ?>
                        </span>
                    </span>
                <?php }
                ?>
            </div>

<?php }
    ?>
</div>
