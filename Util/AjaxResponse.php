<?php

namespace Sli\AuxBundle\Util;

use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use \Symfony\Component\HttpFoundation\JsonResponse instead
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class AjaxResponse extends Response
{
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $headers = array_merge($headers, array(
            'Content-Type' => 'application/json'
        ));

        parent::__construct(json_encode($content), $status, $headers);
    }
}
