<?php
/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


return [
    'commission'                                    => 'Commission',
    'commissions'                                   => 'Commissions',
    'invoice_commission'                            => 'Commission',
    'recurring_invoice_commission'                  => 'RI Commission',
    'invoice_commission'                            => 'Invoice Commission',
    'commission_type_form'                          => 'Create Commission Type',
    'commission_type_edit'                          => 'Edit Commission Type',
    'commission_type'                               => 'Commission Type',
    'commission_types'                              => 'Commission Types',
    'create_commission_type'                        => 'Create',
    'confirm_delete_commission_type'                => 'Are you sure want to delete',
    'formula'                                       => 'Formula',
    'name'                                          => 'Name',
    'cancelled'                                     => 'Cancelled',
    'new'                                           => 'New',
    'approved'                                      => 'Approved',
    'paid'                                          => 'Paid',
    'note'                                          => 'Note',
    'add'                                           => 'Add Commissions',
    'status'                                        => 'Status',
    'select_type'                                   => 'Select Type',
    'select_user'                                   => 'Select User',
    'select_status'                                 => 'Select Status',
    'select'                                        => ' --Select-- ',
    'stop_date'                                     => 'Stop Date',
    'total'                                         => 'Total',
    'create'                                        => 'Create Commission',
    'edit'                                          => 'Edit Commission',
    'create_recurring'                              => 'Create Recurring Commission',
    'edit_recurring'                                => 'Edit Recurring Commission',
    'user'                                          => 'Salesperson',
    'method'                                        => 'Method',
    'report'                                        => 'Commission Reports',
    'manual_entry'                                  => 'Manual Entry',
    'invoice_item'                                  => 'Invoice Item',
    'formula_validate'                              => 'Please check your formula to make sure the references are correct. Review the help text for examples.',
    'invoice_commission_recurring'                  => 'Recurring Invoice Commission Reports',
    'cannot_delete_type'                            => 'Sorry, cannot delete this commission type. It is being used on one or more invoices and/or recurring invoices.',
    'commission_formula_notes'                      => '<br> <b><u>Commission Formula Examples:</u></b> <br> <b>15% commission on line item subtotal:</b> <span>$invoice_items->amount->subtotal * .15</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br> <b>$1.25 commission for each item on an invoice line:</b> <span>$invoice_items->quantity * 1.25</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br> <b>3% commission on the entire invoice total:</b> <span>$invoice->total * .03</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br> <b>3% commission on the entire recurring invoice total:</b> <span>$recurring_invoice->amount->total * .03</span> <i class="fa fa-clipboard copy-to-clipboard clickable" title="Copy To Clipboard"></i><br>',
    'commission_manual_entry_notes'                 => 'A Manual Entry method allows you to enter the commission amount on the invoice directly and has no associated formula.',
    'copied'                                        => 'Formula copied to clipboard',
    'product'                                       => 'Product',
    'sub_total'                                     => 'Sub Total',
    'search_commissions'                            => 'Search Commissions',
    'invoice_commission_created'                    => 'Recurring Invoice Commission #:commission_id for :item, Invoice #:invoice_number was created',
    'recurring_invoice_commission_created'          => 'Commission #:commission_id for :item, Invoice #:invoice_number was created',
    'commission_amount'                             => 'Commission Amount :amount',
    'invoice_commission_updated'                    => 'Commission #:commission_id for :item, Invoice #:invoice_number was updated',
    'recurring_invoice_commission_updated'          => 'Recurring Invoice Commission #:commission_id for :item, Invoice #:invoice_number was updated',
    'invalid-formula'                               => 'Please enter valid formula',
    'commission_formula_note'                       => 'After each element please leave SPACE on formula to correctly validate it.',
    'active'                                        => 'Active',
    'inactive'                                      => 'Inactive',
    'bulk_invoice_commission_change_status_warning' => 'Are you sure you wish to change the status of the selected invoice commission(s)?',
    'recurring_commissions'                         => 'Recurring Commissions',
    'report_commission'                             => 'Report Commission',
];
