<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification & Flash Messages (English)
    |--------------------------------------------------------------------------
    */
    // Users
    'user_created' => 'New user account created successfully.',
    'user_updated' => 'User account data updated successfully.',
    'user_role_updated' => 'User role updated successfully.',
    'user_activated' => 'User account reactivated successfully.',
    'user_deactivated' => 'User account deactivated successfully.',
    'user_password_reset' => 'Password reset successfully for :name.',
    'user_deleted' => 'User account deleted successfully from the system.',

    // Protections
    'cannot_demote_primary_admin' => 'The Primary Administrator account role cannot be changed.',
    'cannot_demote_self' => 'You cannot demote your own account role.',
    'cannot_deactivate_primary_admin' => 'The Primary Administrator account cannot be deactivated.',
    'cannot_deactivate_self' => 'You cannot deactivate your own account.',
    'cannot_delete_primary_admin' => 'The Primary Administrator account cannot be deleted.',
    'cannot_delete_self' => 'You cannot delete your own account.',
    'last_active_admin_required' => 'At least one active Administrator account must remain.',

    // Projects
    'project_created' => 'Project data added successfully.',
    'project_updated' => 'Project data updated successfully.',
    'project_deleted' => 'Project deleted successfully.',
    'project_cannot_delete_has_schedules' => 'Project cannot be deleted because it still has associated Project Schedules. Please remove schedules first.',
    'project_cannot_delete_has_budgets' => 'Project cannot be deleted because it has recorded budget entries.',

    // Schedules
    'schedule_created' => 'Schedule stage added successfully.',
    'schedule_updated' => 'Schedule stage updated successfully.',
    'schedule_deleted' => 'Schedule stage deleted successfully.',
    'schedule_cannot_delete_has_progress' => 'Schedule cannot be deleted because it has recorded progress history.',
    'progress_recorded' => 'Progress recorded successfully.',

    // Materials
    'material_created' => 'New material added to inventory successfully.',
    'material_updated' => 'Material data [:code] updated successfully.',
    'material_deleted' => 'Material [:code] deleted from inventory.',
    'material_has_transactions' => 'Material has transaction history and cannot be deleted.',
    'insufficient_stock' => 'Failed to issue material! Only :stock :unit in stock, requested: :requested :unit.',
    'stock_adjusted' => 'Stock adjustment and inventory audit recorded successfully.',

    // Procurement
    'supplier_created' => 'Supplier added successfully.',
    'supplier_updated' => 'Supplier data updated successfully.',
    'po_created' => 'PO created. New stock will be added upon goods receipt.',
    'po_received' => 'Goods received and inventory stock updated.',
    'po_paid' => 'Supplier payment recorded in cash flow.',

    // Workforce
    'worker_created' => 'New worker [:code] :name added successfully.',
    'worker_updated' => 'Worker [:code] :name updated successfully.',
    'worker_deleted' => 'Worker [:code] :name deleted from system.',
    'assignment_created' => 'Worker assignment saved.',
    'attendance_recorded' => 'Worker attendance and actual labor cost recorded.',
    'wages_paid' => 'Wage payment recorded in cash flow.',

    // Finance & Budgets
    'cash_flow_created' => 'Cash flow entry recorded successfully.',
    'expense_created' => 'Expense entry recorded successfully.',
    'expense_updated' => 'Expense data updated successfully.',
    'expense_deleted' => 'Expense record deleted successfully.',
];
