<?php

namespace Abtechi\Laravel\Infrastructure;

use Illuminate\Http\Response;

/**
 * Class JSONPresenter
 * @package App\Presenters
 */
class JSONPresenter implements InterfacePresenter
{

    /**
     * @param array|string $input
     * @param $code
     * @return Response
     */
    public function format($input, $code)
    {
        return json_decode($input);
    }
}