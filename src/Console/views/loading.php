<?php

declare(strict_types=1);

/** @var ?string $spinner */
/** @var string $message */
?>
<div class="mx-2 my-1 space-x-1">
    <span class="text-blue"><?php echo $spinner ?? '⣿' ?></span>
    <span><?php echo $message; ?></span>
</div>
