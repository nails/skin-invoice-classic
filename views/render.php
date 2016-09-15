<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            <?=$invoice->ref?> - <?=APP_NAME?>
        </title>
        <style type="text/css">
            <?php

            require dirname(__FILE__) . '/../assets/css/styles.css';

            ?>
        </style>
    </head>
    <body class="<?=empty($isPdf) ? 'is-html' : 'is-pdf'?>">
        <?php

        if (empty($isPdf)) {

            ?>
            <div id="paybar">
                <div id="paybar-container">
                    <a href="<?=$invoice->urls->payment?>" class="btn btn-primary">
                        Pay Now
                    </a>
                    <a href="<?=$invoice->urls->download?>" class="btn btn-primary pull-right">
                        Download
                    </a>
                </div>
            </div>
            <?php
        }

        ?>
        <div id="container">
            <header class="clearfix">
                <div id="logo">
                    <?php

                    if (!isset($paths)) {
                        $paths = array();
                    }

                    if (defined('FCPATH') && defined('BASE_URL')) {

                        $paths[] = array(
                            FCPATH . 'assets/img/logo.png',
                            BASE_URL . 'assets/img/logo.png'
                        );

                        $paths[] = array(
                            FCPATH . 'assets/img/logo.jpg',
                            BASE_URL . 'assets/img/logo.jpg'
                        );

                        $paths[] = array(
                            FCPATH . 'assets/img/logo.gif',
                            BASE_URL . 'assets/img/logo.gif'
                        );

                        $paths[] = array(
                            FCPATH . 'assets/img/logo/logo.png',
                            BASE_URL . 'assets/img/logo/logo.png'
                        );

                        $paths[] = array(
                            FCPATH . 'assets/img/logo/logo.jpg',
                            BASE_URL . 'assets/img/logo/logo.jpg'
                        );

                        $paths[] = array(
                            FCPATH . 'assets/img/logo/logo.gif',
                            BASE_URL . 'assets/img/logo/logo.gif'
                        );
                    }

                    foreach ($paths as $path) {
                        if (is_file($path[0])) {
                            echo '<img src="' . $path[1] . '" />';
                            break;
                        }
                    }

                    ?>
                </div>
                <h1><?=$invoice->ref?></h1>
                <div id="state" class="<?=strtolower(str_replace('_', '-', $invoice->state->id))?>">
                        <?=strtoupper($invoice->state->label)?>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <div id="project">
                                    <div>
                                        <span>CUSTOMER</span>
                                        <?=$invoice->customer->label?>
                                    </div>
                                    <?php

                                    if (!empty($invoice->customer->vat_number)) {

                                        ?>
                                        <div>
                                            <span>VAT NUMBER</span>
                                            <?=$invoice->customer->vat_number?>
                                        </div>
                                        <?php
                                    }

                                    $aAddress = array(
                                        $invoice->customer->billing_address->line_1,
                                        $invoice->customer->billing_address->line_2,
                                        $invoice->customer->billing_address->town,
                                        $invoice->customer->billing_address->county,
                                        $invoice->customer->billing_address->postcode,
                                        $invoice->customer->billing_address->country
                                    );
                                    $aAddress = array_filter($aAddress);

                                    if (!empty($aAddress)) {

                                        ?>
                                        <div>
                                            <span>ADDRESS</span>
                                            <?=implode('<br />', $aAddress)?>
                                        </div>
                                        <?php
                                    }

                                    ?>
                                    <div>
                                        <span>EMAIL</span>
                                        <?php

                                        if (!empty($invoice->customer->billing_email)) {

                                            echo $invoice->customer->billing_email;

                                        } else {

                                            echo $invoice->customer->email;
                                        }

                                        ?>
                                    </div>
                                    <div>
                                        <span>DATE</span>
                                        <?=$invoice->dated->formatted?>
                                    </div>
                                    <div>
                                        <span>DUE DATE</span>
                                        <?=$invoice->due->formatted?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div id="company" class="clearfix">
                                    <?=$business->name ? '<div>' . $business->name . '</div>' : ''?>
                                    <?=$business->address ? '<div>' . nl2br($business->address) . '</div>' : ''?>
                                    <?=$business->telephone ? '<div>' . $business->telephone . '</div>' : ''?>
                                    <?=$business->email ? '<div>' . $business->email . '</div>' : ''?>
                                    <?=$business->vat_number ? '<div>VAT: ' . $business->vat_number . '</div>' : ''?>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </header>
            <main>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="desc">DESCRIPTION</th>
                            <th class="price">PRICE</th>
                            <th class="qty">QTY</th>
                            <th class="tax">TAX</th>
                            <th class="total">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->items->data as $oItem) {

                            ?>
                            <tr>
                                <td class="desc">
                                    <?=$oItem->label?>
                                    <div><?=$oItem->body?></div>
                                </td>
                                <td class="price">
                                    <?=$oItem->unit_cost->localised_formatted?>
                                </td>
                                <td class="qty">
                                    <?php

                                    echo $oItem->quantity;
                                    if ($oItem->unit->id !== 'NONE') {
                                        echo ' ' . $oItem->unit->label;
                                    }

                                    ?>
                                </td>
                                <td class="tax">
                                    <?=!empty($oItem->tax) ? $oItem->tax->rate : 0?>%
                                </td>
                                <td class="total">
                                    <?=$oItem->totals->localised_formatted->grand?>
                                </td>
                            </tr>
                            <?php
                        }

                        ?>
                        <tr class="total sub">
                            <td colspan="4">SUBTOTAL</td>
                            <td class="total">
                                <?=$invoice->totals->localised_formatted->sub?>
                            </td>
                        </tr>
                        <tr class="total tax">
                            <td colspan="4">TAX</td>
                            <td class="total">
                                <?=$invoice->totals->localised_formatted->tax?>
                            </td>
                        </tr>
                        <tr class="total grand">
                            <td colspan="4" class="grand total">GRAND TOTAL</td>
                            <td class="grand total">
                                <?=$invoice->totals->localised_formatted->grand?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php

                if ($invoice->refunds->count) {

                    ?>
                    <div class="refunds">
                        <div class="refunds__header">REFUNDS</div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-left">Reference</th>
                                    <th class="text-left">Reason</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($invoice->refunds->data as $oRefund) {

                                    if (in_array($oRefund->status->id, array('COMPLETE', 'PROCESSING'))) {
                                        ?>
                                        <tr>
                                            <td class="text-left"><?=$oRefund->ref?></td>
                                            <td class="text-left"><?=$oRefund->reason?></td>
                                            <td><?=$oRefund->amount->localised_formatted?></td>
                                            <td><?=toUserDateTime($oRefund->created)?></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }

                if (!empty($invoice->additional_text)) {

                    ?>
                    <div class="notices">
                        <div class="notices__header">ADDITIONAL INFORMATION</div>
                        <div class="notices__body"><?=$invoice->additional_text?></div>
                    </div>
                    <?php
                }

                ?>
            </main>
        </div>
    </body>
</html>
