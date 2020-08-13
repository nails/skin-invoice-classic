<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>
            <?=$invoice->ref?> - <?=\Nails\Config::get('APP_NAME')?>
        </title>
        <style type="text/css">
            <?php
            require dirname(__FILE__) . '/../assets/css/styles.min.css';
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
                <?php

                $sLogo = logoDiscover();
                if (!empty($sLogo)) {
                    echo '<div id="logo">' . img($sLogo) . '</div>';
                }

                ?>
                <h1><?=$invoice->ref?></h1>
                <div id="state" class="<?=strtolower(str_replace('_', '-', $invoice->state->id))?>">
                    <?=strtoupper($invoice->state->label)?>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="project-details">
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

                                ?>
                                <div>
                                    <span>EMAIL</span>
                                    <?=$invoice->customer->billing_email ?: $invoice->customer->email?>
                                </div>
                                <div>
                                    <span>DATE</span>
                                    <?=$invoice->dated->formatted?>
                                </div>
                                <div>
                                    <span>DUE DATE</span>
                                    <?=$invoice->due->formatted?>
                                </div>
                            </td>
                            <?php

                            $oBillingAddress = $invoice->billingAddress();
                            if (!empty($oBillingAddress)) {
                                ?>
                                <td class="project-details">
                                    <div>
                                        <span>BILLING<br>ADDRESS</span>
                                        <?=$oBillingAddress->formatted()->withSeparator('<br />')?>
                                    </div>
                                </td>
                                <?php
                            }

                            $oDeliveryAddress = $invoice->deliveryAddress();
                            if (!empty($oDeliveryAddress)) {
                                ?>
                                <td class="project-details">
                                    <div>
                                        <span>DELIVERY<br>ADDRESS</span>
                                        <?=$oDeliveryAddress->formatted()->withSeparator('<br />')?>
                                    </div>
                                </td>
                                <?php
                            }

                            $aCompanyDetails = array_filter([
                                $business->name ? '<div>' . $business->name . '</div>' : null,
                                $business->address ? '<div>' . nl2br($business->address) . '</div>' : null,
                                $business->telephone ? '<div>' . $business->telephone . '</div>' : null,
                                $business->email ? '<div>' . $business->email . '</div>' : null,
                                $business->vat_number ? '<div>VAT: ' . $business->vat_number . '</div>' : null,
                            ]);

                            if (!empty($aCompanyDetails)) {
                                ?>
                                <td>
                                    <div class="company-details clearfix">
                                        <?=implode(PHP_EOL, $aCompanyDetails)?>
                                    </div>
                                </td>
                                <?php
                            }

                            ?>
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
                                    <?=html_entity_decode($oItem->unit_cost->formatted)?>
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
                                    <?=html_entity_decode($oItem->totals->formatted->grand)?>
                                </td>
                            </tr>
                            <?php
                        }

                        ?>
                        <tr class="total sub">
                            <td colspan="4">SUBTOTAL</td>
                            <td class="total">
                                <?=$invoice->totals->formatted->sub?>
                            </td>
                        </tr>
                        <tr class="total tax">
                            <td colspan="4">TAX</td>
                            <td class="total">
                                <?=$invoice->totals->formatted->tax?>
                            </td>
                        </tr>
                        <tr class="total grand">
                            <td colspan="4" class="grand total">GRAND TOTAL</td>
                            <td class="grand total">
                                <?=$invoice->totals->formatted->grand?>
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
                                    if (in_array($oRefund->status->id, ['COMPLETE', 'PROCESSING'])) {
                                        ?>
                                        <tr>
                                            <td class="text-left"><?=$oRefund->ref?></td>
                                            <td class="text-left"><?=$oRefund->reason?></td>
                                            <td><?=$oRefund->amount->formatted?></td>
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
