<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Controllers;

use Addons\TimeTracking\Models\TimeTrackingProject;
use Addons\TimeTracking\Models\TimeTrackingTask;
use FI\Http\Controllers\Controller;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Invoices\Events\InvoiceModified;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;

class TaskBillController extends Controller
{
    public function create()
    {
        $project = TimeTrackingProject::find(request('projectId'));

        $invoices       = [];

        $clientInvoices = $project->client->invoices()->select('invoices.*')->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')->where('invoice_amounts.paid', '<=', '0')->orderBy('created_at', 'desc')->statusIn(['draft', 'sent'])->get();

        foreach ($clientInvoices as $invoice)
        {
            $invoices[$invoice->id] = $invoice->formatted_created_at . ' - ' . $invoice->number . ' ' . $invoice->summary;
        }

        return view('time_tracking._task_bill_modal')
            ->with('project', $project)
            ->with('taskIds', request('taskIds'))
            ->with('invoices', $invoices)
            ->with('invoiceCount', count($invoices))
            ->with('documentNumberSchemes', DocumentNumberScheme::getList());
    }

    public function store()
    {
        $project = TimeTrackingProject::find(request('project_id'));

        $tasks = TimeTrackingTask::getSelect()
                                 ->orderBy('display_order')
                                 ->orderBy('created_at')
                                 ->whereIn('id', json_decode(request('task_ids')))->get();

        if (request('how_to_bill') == 'new')
        {
            $invoice = Invoice::create([
                'client_id'                 => $project->client_id,
                'company_profile_id'        => $project->company_profile_id,
                'document_number_scheme_id' => request('document_number_scheme_id'),
                'user_id'                   => auth()->user()->id
            ]);
        }
        else if (request('how_to_bill') == 'existing')
        {
            $invoice = Invoice::find(request('invoice_id'));
        }

        foreach ($tasks as $task)
        {
            InvoiceItem::create([
                'invoice_id'    => $invoice->id,
                'name'          => $task->name,
                'description'   => $task->description,
                'quantity'      => $task->hours,
                'price'         => $project->hourly_rate,
                'tax_rate_id'   => config('fi.itemTaxRate'),
                'tax_rate_2_id' => config('fi.itemTax2Rate'),
            ]);

            $task->billed     = 1;
            $task->invoice_id = $invoice->id;
            $task->save();
        }

        event(new InvoiceModified($invoice));

        return route('invoices.edit', [$invoice->id]);
    }
}