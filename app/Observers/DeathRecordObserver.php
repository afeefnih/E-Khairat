<?php

namespace App\Observers;

use App\Models\DeathRecord;
use App\Models\User;
use App\Models\Dependent;

class DeathRecordObserver
{
    /**
     * Handle the DeathRecord "created" event.
     */
    public function created(DeathRecord $deathRecord): void
    {
        // Update status for the deceased entity
        $this->updateDeceasedStatus($deathRecord);
    }

    /**
     * Handle the DeathRecord "updated" event.
     */
    public function updated(DeathRecord $deathRecord): void
    {
        // If the deceased_type or deceased_id changed, update statuses
        if ($deathRecord->isDirty('deceased_type') || $deathRecord->isDirty('deceased_id')) {
            $this->updateDeceasedStatus($deathRecord);
        }
    }

    /**
     * Handle the DeathRecord "deleted" event.
     */
    public function deleted(DeathRecord $deathRecord): void
    {
        // Optional: Restore status when a death record is deleted
        // This depends on your business logic - whether deleting a death record
        // should revert the deceased status

        // Example:
        // if ($deathRecord->deceased_type === User::class && $deathRecord->deceased) {
        //     $deathRecord->deceased->status = 'active';
        //     $deathRecord->deceased->save();
        // }
    }

    /**
     * Update the status of the deceased entity.
     */
    private function updateDeceasedStatus(DeathRecord $deathRecord): void
    {
        // For User deaths
        if ($deathRecord->deceased_type === User::class && $deathRecord->deceased) {
            $user = $deathRecord->deceased;

            // Here you would update user status based on your model structure
            // Example: If you have a 'status' column
            // $user->status = 'deceased';
            // $user->save();

            // You might also want to handle the user's dependents
            // For example, you could mark them as "orphaned" or assign them to another member
            // depending on your system's requirements
        }

        // For Dependent deaths
        if ($deathRecord->deceased_type === Dependent::class && $deathRecord->deceased) {
            $dependent = $deathRecord->deceased;

            // Update dependent status
            // Example: If you have a 'status' column
            // $dependent->status = 'deceased';
            // $dependent->save();
        }

        // For legacy records (using dependent_id directly)
        if ($deathRecord->dependent_id && !$deathRecord->deceased) {
            $dependent = Dependent::find($deathRecord->dependent_id);
            if ($dependent) {
                // Update dependent status
                // $dependent->status = 'deceased';
                // $dependent->save();
            }
        }
    }
}
