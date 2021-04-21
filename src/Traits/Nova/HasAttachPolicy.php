<?php

namespace Lomkit\Laravel\Traits\Nova;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;

trait HasAttachPolicy
{
    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttachAny(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'attachAny'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
            ? Gate::check($method, [$this->model()])
            : $this->userCan($request);
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $relationship
     * @return bool
     */
    public function authorizedToDetach(NovaRequest $request, $model, $relationship)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'detach'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
            ? Gate::check($method, [$this->model(), $model])
            : $this->userCan($request);
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttach(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'attach'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
            ? Gate::check($method, [$this->model(), $model])
            : $this->userCan($request);
    }

    public function userCan(NovaRequest $request) {
        return $request->user()->can("update {$this->model()->getTable()}");
    }
}
