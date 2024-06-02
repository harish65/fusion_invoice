<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Settings\Requests;

use FI\Modules\Settings\Rules\ValidFile;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'setting.invoicesDueAfter'  => trans('fi.invoices_due_after'),
            'setting.quotesExpireAfter' => trans('fi.quotes_expire_after'),
            'setting.pdfBinaryPath'     => trans('fi.binary_path'),
            'setting.feePercentage'     => trans('fi.fee_percentage'),
            'setting.enableOppFees'     => trans('fi.enable_online_payment_processing_fees'),
        ];
    }

    public function rules()
    {
        $rules = [
            'setting.invoicesDueAfter'                        => 'required|numeric',
            'setting.quotesExpireAfter'                       => 'required|numeric',
            'setting.pdfBinaryPath'                           => ['required_if:setting.pdfDriver,wkhtmltopdf', new ValidFile],
            'setting.dashboardWidgetsFromDate'                => ['required_if:setting.dashboardWidgetsDateOptions,custom_date_range'],
            'setting.dashboardWidgetsToDate'                  => ['required_if:setting.dashboardWidgetsDateOptions,custom_date_range'],
            'setting.mailFromAddress'                         => 'required|email',
            'setting.feePercentage'                           => ['required_if:setting.enableOppFees,1', 'numeric', 'gt:0'],
            'setting.quoteCustomMailTemplate'                 => ['required_if:setting.quoteUseCustomTemplate,custom_mail_template'],
            'setting.quoteApprovedCustomMailTemplate'         => ['required_if:setting.quoteApprovedUseCustomTemplate,custom_mail_template'],
            'setting.quoteRejectedUseCustomTemplate'          => ['required_if:setting.quoteRejectedUseCustomTemplate,custom_mail_template'],
            'setting.overdueInvoiceCustomMailTemplate'        => ['required_if:setting.overdueInvoiceUseCustomTemplate,custom_mail_template'],
            'setting.invoiceCustomMailTemplate'               => ['required_if:setting.invoiceUseCustomTemplate,custom_mail_template'],
            'setting.creditMemoCustomMailTemplate'            => ['required_if:setting.creditMemoUseCustomTemplate,custom_mail_template'],
            'setting.paymentReceiptCustomMailTemplate'        => ['required_if:setting.paymentReceiptUseCustomTemplate,custom_mail_template'],
            'setting.upcomingPaymentNoticeCustomMailTemplate' => ['required_if:setting.upcomingPaymentNoticeUseCustomTemplate,custom_mail_template'],
            'setting.secure_link_expire_day'                  => ['required_if:setting.secure_link,1', 'numeric'],
        ];

        if ($this->request->get('email-test'))
        {
            $rules = array_merge($rules, ['setting.testEmailAddress' => 'required|email']);
        }

        foreach (config('fi.settingValidationRules') as $settingValidationRules)
        {
            $rules = array_merge($rules, $settingValidationRules['rules']);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'test_email_address.required'                                 => trans('fi.test-email-required'),
            'setting.mailFromAddress.required'                            => trans('fi.mail-from-required'),
            'setting.mailFromAddress.email'                               => trans('fi.mail-from-required'),
            'setting.dashboardWidgetsFromDate.required_if'                => trans('fi.dashboard-widget-from-date-required'),
            'setting.dashboardWidgetsToDate.required_if'                  => trans('fi.dashboard-widget-to-date-required'),
            'setting.feePercentage.required_if'                           => trans('fi.opp-fee-percentage-required'),
            'setting.feePercentage.gt'                                    => trans('fi.opp-fee-percentage-gt-zero'),
            'setting.quoteCustomMailTemplate.required_if'                 => trans('fi.quote_custom_email_body_require'),
            'setting.quoteApprovedCustomMailTemplate.required_if'         => trans('fi.quote_custom_approve_email_body_require'),
            'setting.quoteRejectedUseCustomTemplate.required_if'          => trans('fi.quote_reject_email_body_require'),
            'setting.overdueInvoiceCustomMailTemplate.required_if'        => trans('fi.overdue_invoice_email_body_require'),
            'setting.invoiceCustomMailTemplate.required_if'               => trans('fi.invoice_email_body_require'),
            'setting.creditMemoCustomMailTemplate.required_if'            => trans('fi.credit_memo_email_body_require'),
            'setting.paymentReceiptCustomMailTemplate.required_if'        => trans('fi.payment_receipt_email_body_require'),
            'setting.upcomingPaymentNoticeCustomMailTemplate.required_if' => trans('fi.upcoming_payment_notice_email_body_require'),
            'setting.secure_link_expire_day.required_if'                  => trans('fi.expire_time_period'),
        ];
    }
}