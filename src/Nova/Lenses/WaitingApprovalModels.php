<?php

namespace Lomkit\Laravel\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;
use Lomkit\Laravel\Traits\Nova\Lenses\LensInheritParent;

class WaitingApprovalModels extends Lens
{
    use LensInheritParent;

    public $name = 'Waiting For Approval';

    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->waitingApproval()
        ));
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey() {
        return 'waiting-approval-models';
    }
}
