<?php

namespace App\Observers;

use App\Models\Category;
use Bschmitt\Amqp\Message;

class CategoryObserve
{
    /**
     * Handle the category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        $message = new Message(
            $category->toJson()
        );
        \Amqp::publish('model.category.created', $message);
    }

    /**
     * Handle the category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        $message = new Message(
            $category->toJson()
        );
        \Amqp::publish('model.category.updated', $message);
    }

    /**
     * Handle the category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $message = new Message(json_encode(['id' => $category->id]));
        \Amqp::publish('model.category.deleted', $message);
    }

    /**
     * Handle the category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }
}
