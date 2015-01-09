<?php

class Application_Common_HTTPHelper
{
    /**
     * Returns start and end DateTime vars from given 
     * HTTP Request object
     *
     * @param Request
     * @return array(start DateTime, end DateTime)
     */
    public static function getStartEndFromRequest($request)
    {
        return Application_Common_DateHelper::getStartEnd(
            $request->getParam("start", null),
            $request->getParam("end", null),
            $request->getParam("timezone", null)
        );
    }
}
