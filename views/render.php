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
                                        <span>CLIENT</span>
                                        @todo CUSTOMER NAME
                                    </div>
                                    <div>
                                        <span>ADDRESS</span>
                                        @todo CUSTOMER ADDRESS
                                    </div>
                                    <div>
                                        <span>EMAIL</span>
                                        @todo CUSTOMER EMAIL
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
                                    <div>@todo BUSINESS NAME</div>
                                    <div>@todo BUSINESS ADDRESS</div>
                                    <div>@todo BUSINESS PHONE</div>
                                    <div>@todo BUSINESS EMAIL</div>
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
                            <th>PRICE</th>
                            <th>QTY</th>
                            <th>TOTAL</th>
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
                                <td class="unit">
                                    <?=$oItem->unit_cost->localised_formatted?>
                                </td>
                                <td class="qty">
                                    <?=$oItem->quantity?>
                                </td>
                                <td class="total">
                                    <?=$oItem->totals->localised_formatted->grand?>
                                </td>
                            </tr>
                            <?php
                        }

                        ?>
                        <tr>
                            <td colspan="3">SUBTOTAL</td>
                            <td class="total">
                                <?=$invoice->totals->localised_formatted->sub?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">TAX</td>
                            <td class="total">
                                <?=$invoice->totals->localised_formatted->tax?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="grand total">GRAND TOTAL</td>
                            <td class="grand total">
                                <?=$invoice->totals->localised_formatted->grand?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php

                if (!empty($invoice->additional_text)) {

                    ?>
                    <div id="notices">
                        <div>NOTICE:</div>
                        <div class="notice"><?=$invoice->additional_text?></div>
                    </div>
                    <?php
                }

                ?>
            </main>
        </div>
    </body>
</html>