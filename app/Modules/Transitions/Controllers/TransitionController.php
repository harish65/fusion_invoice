<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Transitions\Controllers;

use FI\Modules\Clients\Models\Client;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Transitions\Models\Transitions;
use FI\Http\Controllers\Controller;
use FI\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class TransitionController extends Controller
{

    public function userTransitions(Request $request, Client $client)
    {
        $filterUsers   = ($request->has('user') && !empty($request->get('user'))) ? $request->get('user') : [];
        $filterModules = ($request->has('filter_module') && !empty($request->get('filter_module'))) ? $request->get('filter_module') : [];
        $customSearch  = ($request->has('custom_search') && !empty($request->get('custom_search'))) ? $request->get('custom_search') : null;
        $clientId      = $client->id;

        if ($customSearch && empty($filterModules))
        {
            $filterModules = array_keys(Transitions::mapModule());
        }

        $transitions          = Transitions::getPaginatedTransitions($filterUsers, $filterModules, $customSearch, $clientId);
        $monthWiseTransitions = [];

        foreach ($transitions as $transition)
        {
            $key                          = trans(strtolower('fi.month_' . $transition->created_at->format('F'))) . ' ' . $transition->created_at->format('Y');
            $monthWiseTransitions[$key][] = $transition;
        }
        return view('transitions.list')
            ->with('transitions', $transitions)
            ->with('monthWiseTransitions', $monthWiseTransitions)
            ->with('hideHeader', true);
    }

    public function invoiceTransitions(Request $request, Invoice $invoice)
    {

        $filterUsers   = ($request->has('user') && !empty($request->get('user'))) ? $request->get('user') : [];
        $filterModules = ['invoices', 'credit_applied', 'credit_memo', 'notes'];
        $customSearch  = ($request->has('custom_search') && !empty($request->get('custom_search'))) ? $request->get('custom_search') : null;

        if ($customSearch && empty($filterModules))
        {
            $filterModules = array_keys(Transitions::mapModule());
        }

        $transitions          = Transitions::getInvoicePaginatedTransitions($filterUsers, $filterModules, $customSearch, $clientId = null, $invoice->id, 'invoice');
        $monthWiseTransitions = [];

        foreach ($transitions as $transition)
        {
            $key                          = trans(strtolower('fi.month_' . $transition->created_at->format('F'))) . ' ' . $transition->created_at->format('Y');
            $monthWiseTransitions[$key][] = $transition;
        }

        $filterUsersData = [];
        if (auth()->user()->user_type == 'admin')
        {
            $users = User::select('id', 'name')->get()->toArray();
            foreach ($users as $user)
            {
                $filterUsersData[$user['id']] = $user['name'];
            }
        }

        return view('transitions.invoices.list')
            ->with('transitions', $transitions)
            ->with('invoiceId', $invoice->id)
            ->with('monthWiseTransitions', $monthWiseTransitions)
            ->with('filterUsersData', $filterUsersData)
            ->with('hideHeader', true);
    }

    public function refresh()
    {
        Cookie::queue(Cookie::forget('eventType'));

        Cookie::queue(Cookie::forget('userIds'));

        Cookie::queue(Cookie::forget('time_line_search'));
    }

    public function widgetList(Request $request)
    {
        if ($request->get('custom_search'))
        {
            Cookie::queue(Cookie::forget('time_line_search'));
            Cookie::queue(Cookie::make('time_line_search', $request->get('custom_search', null)));
        }
        else
        {
            Cookie::queue(Cookie::forget('time_line_search'));
        }

        if (Cookie::get('eventType'))
        {
            $result = $request->get('filter_module') ? 'Yes' : 'No';
            if ($result == 'No')
            {
                $filter_module = [];
                foreach (explode(",", Cookie::get('eventType')) as $key => $value)
                {
                    $filter_module[$key] = $value;
                }
                $request->request->add(['filter_module' => $filter_module]);
                Cookie::queue(Cookie::forget('eventType'));
            }
            else
            {
                Cookie::queue(Cookie::forget('eventType'));
            }
        }
        $filterUsers   = ($request->has('user') && !empty($request->get('user'))) ? $request->get('user') : [];
        $filterModules = ($request->has('filter_module') && !empty($request->get('filter_module'))) ? $request->get('filter_module') : [];

        $filterUser = [];
        Cookie::queue(Cookie::make('eventType', implode(",", $filterModules)));

        if ($request->get('user'))
        {
            foreach ($request->get('user') as $val)
            {
                $filterUser[] = $val;
            }
        }

        Cookie::queue(Cookie::make('userIds', implode(",", $filterUser)));

        $customSearch = ($request->has('custom_search') && !empty($request->get('custom_search'))) ? $request->get('custom_search') : null;

        if ($customSearch == null && (Cookie::get('time_line_search') != ''))
        {
            $customSearch = Cookie::get('time_line_search');
            Cookie::queue(Cookie::forget('time_line_search'));
        }
        if ($customSearch && empty($filterModules))
        {
            $filterModules = array_keys(Transitions::mapModule());
        }

        $transitions          = Transitions::getPaginatedTransitions($filterUsers, $filterModules, $customSearch);
        $monthWiseTransitions = [];

        foreach ($transitions as $transition)
        {
            $key                          = trans(strtolower('fi.month_' . $transition->created_at->format('F'))) . ' ' . $transition->created_at->format('Y');
            $monthWiseTransitions[$key][] = $transition;
        }
        return view('transitions.widget.list')
            ->with('transitions', $transitions)
            ->with('monthWiseTransitions', $monthWiseTransitions)
            ->with('hideHeader', true);
    }
}
