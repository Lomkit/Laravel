<?php

namespace Lomkit\Laravel\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;
use Lomkit\Laravel\Traits\Nova\Lenses\LensInheritParent;

class WaitingTranslationModels extends Lens
{
    use LensInheritParent;

    public $name = 'Waiting For Translation';

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
            $query->waitingTranslation()
        ));
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey() {
        return 'waiting-translation-models';
    }
}
