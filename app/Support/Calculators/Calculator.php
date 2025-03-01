<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\Calculators;

abstract class Calculator
{
    /**
     * The id of the quote or invoice.
     *
     * @var int
     */
    protected $id;

    /**
     * The id of the quote or invoice.
     *
     * @var string
     */
    protected $type;

    /**
     * An array to store items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * An array to store calculated item amounts.
     *
     * @var array
     */
    protected $calculatedItemAmounts = [];

    /**
     * An array to store overall calculated amounts.
     *
     * @var array
     */
    protected $calculatedAmount = [];

    /**
     * Whether or not the document is canceled.
     *
     * @var boolean
     */
    protected $isCanceled = false;

    protected $discount;

    protected $onlinePaymentProcessingFee;

    protected $convenienceCharges;

    protected $clientOnlinePaymentProcessingFee;

    /**
     * Initialize the calculated amount array.
     */
    public function __construct()
    {
        $this->calculatedAmount = [
            'subtotal' => 0,
            'discount' => 0,
            'tax'      => 0,
            'total'    => 0,
        ];
    }

    /**
     * Sets the id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Sets the id.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setIsCanceled($isCanceled)
    {
        $this->isCanceled = $isCanceled;
    }

    public function setOnlinePaymentProcessingFee($onlinePaymentProcessingFee)
    {
        $this->onlinePaymentProcessingFee = $onlinePaymentProcessingFee;
    }

    public function setConvenienceCharges($convenienceCharges)
    {
        $this->convenienceCharges = $convenienceCharges;
    }

    public function setClientOnlinePaymentProcessingFee($clientOnlinePaymentProcessingFee)
    {
        $this->clientOnlinePaymentProcessingFee = $clientOnlinePaymentProcessingFee;
    }

    /**
     * @param $itemId
     * @param $quantity
     * @param $price
     * @param float $taxRatePercent
     * @param float $taxRate2Percent
     * @param int $taxRate2IsCompound
     * @param int $calculateVat
     * @param string $discountType
     * @param int $discount
     * @param int $previous_price
     */

    public function addItem($itemId, $quantity, $price, $taxRatePercent = 0.00, $taxRate2Percent = 0.00, $taxRate2IsCompound = 0, $calculateVat = 0, $discountType = '', $discount = 0, $previous_price = 0)
    {
        $this->items[] = [
            'itemId'             => $itemId,
            'quantity'           => $quantity,
            'price'              => $price,
            'taxRatePercent'     => $taxRatePercent,
            'taxRate2Percent'    => $taxRate2Percent,
            'taxRate2IsCompound' => $taxRate2IsCompound,
            'calculateVat'       => $calculateVat,
            'discountType'       => $discountType,
            'discount'           => $discount,
            'previous_price'     => $previous_price,
        ];
    }

    /**
     * Call the calculation methods.
     */
    public function calculate()
    {
        $this->calculateItems();
    }

    /**
     * Returns calculated item amounts.
     *
     * @return array
     */
    public function getCalculatedItemAmounts()
    {
        return $this->calculatedItemAmounts;
    }

    /**
     * Returns overall calculated amount.
     *
     * @return array
     */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }

    /**
     * Calculates the items.
     */
    protected function calculateItems()
    {
        foreach ($this->items as $item)
        {
            if ($item['discountType'] != null)
            {
                $price = calculateDiscount($item['discountType'], $item['discount'], $item['price']);
            }
            else
            {
                $price = removeCalculateDiscount($item['discountType'], $item['previous_price'], $item['price']);
            }

            if ($this->type == 'credit_memo')
            {
                $subtotal = round(abs($item['quantity']) * (-1 * abs($price['price'])), 2);
            }
            else
            {
                $subtotal = round($item['quantity'] * $price['price'], 2);
            }

            $discount           = $this->discount > 0 ? $subtotal * ($this->discount / 100) : 0;
            $discountedSubtotal = $subtotal - $discount;

            if ($item['discountType'] && $this->type != 'credit_memo' && config('fi.allowLineItemDiscounts') == 1)
            {
                if ($item['discountType'] == 'percentage')
                {
                    $discountAmount = (($item['price'] * $item['discount']) / 100) * $item['quantity'];
                }
                elseif ($item['discountType'] == 'flat_amount')
                {
                    $discountAmount = $item['discount'];
                }
                else
                {
                    $discountAmount = null;
                }
            }
            else
            {
                $discountAmount = null;
            }

            if ($item['taxRatePercent'])
            {
                if (!$item['calculateVat'])
                {
                    $tax1 = round($discountedSubtotal * ($item['taxRatePercent'] / 100), config('fi.roundTaxDecimals'));
                }
                else
                {
                    $tax1     = $discountedSubtotal - ($discountedSubtotal / (1 + $item['taxRatePercent'] / 100));
                    $subtotal = $subtotal - $tax1;
                }
            }
            else
            {
                $tax1 = 0;
            }

            if ($item['taxRate2Percent'])
            {
                if ($item['taxRate2IsCompound'])
                {
                    $tax2 = round(($discountedSubtotal + $tax1) * ($item['taxRate2Percent'] / 100), config('fi.roundTaxDecimals'));
                }
                else
                {
                    $tax2 = round($discountedSubtotal * ($item['taxRate2Percent'] / 100), config('fi.roundTaxDecimals'));
                }
            }
            else
            {
                $tax2 = 0;
            }

            $taxTotal = $tax1 + $tax2;
            $total    = $subtotal + $taxTotal;

            $this->calculatedItemAmounts[] = [
                'item_id'         => $item['itemId'],
                'subtotal'        => $subtotal,
                'tax_1'           => $tax1,
                'tax_2'           => $tax2,
                'tax'             => $taxTotal,
                'discount_amount' => $discountAmount,
                'total'           => $total,
            ];

            $this->calculatedAmount['subtotal'] += $subtotal;
            $this->calculatedAmount['discount'] += $discount;
            $this->calculatedAmount['tax']      += $taxTotal;
            $this->calculatedAmount['total']    += ($total - $discount);
        }
    }
}