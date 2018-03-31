<?php

namespace App;

trait RecordsActivity
{
    // boot + <trait-name>
    // it acts as if it is a boot method of whatever it belongs to.
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) {
            return;
        }

        foreach (static::getActivitiesToRecord() as $event) {
            // When a new model is created, it runs
            static::$event(function ($model) use ($event) {
                $model->recordEvent($event);
            });
        }

        // This will only fire on a model, not collection.
        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    protected static function getActivitiesToRecord()
    {
        return ['created']; // This is a model event. You could see many events in Model.php
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    protected function recordEvent($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
