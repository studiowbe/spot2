<?php


namespace Studiow\Spot;
/**
 * Class Event
 * @package Studiow\Spot2
 *
 * Defines Spot2 event names as constants
 */

class Event
{

    const BEFORE_SAVE = 'beforeSave';
    const AFTER_SAVE = 'afterSave';

    const BEFORE_INSERT = 'beforeInsert';
    const AFTER_INSERT = 'afterInsert';

    const BEFORE_UPDATE = 'beforeUpdate';
    const AFTER_UPDATE = 'afterUpdate';

    const BEFORE_VALIDATE = 'beforeValidate';
    const AFTER_VALIDATE = 'afterValidate';

    const BEFORE_DELETE = 'beforeDelete';
    const BEFORE_DELETE_CONDITIONS = 'beforeDeleteConditions';
}