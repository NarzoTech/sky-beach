<?php
namespace App\Traits;

use ReflectionClass;

trait PermissionsTrait
{
    public static array $dashboardPermissions = [
        'group_name'  => 'dashboard',
        'permissions' => [
            'dashboard.view',
        ],
    ];

    public static array $adminProfilePermissions = [
        'group_name'  => 'admin profile',
        'permissions' => [
            'admin.profile.view',
            'admin.profile.edit',
            'admin.profile.update',
            'admin.profile.delete',
        ],
    ];

    public static array $adminPermissions = [
        'group_name'  => 'admin',
        'permissions' => [
            'admin.view',
            'admin.create',
            'admin.store',
            'admin.edit',
            'admin.update',
            'admin.delete',
        ],
    ];

    public static array $rolePermissions = [
        'group_name'  => 'role',
        'permissions' => [
            'role.view',
            'role.create',
            'role.assign',
            'role.edit',
            'role.delete',
        ],
    ];

    public static array $settingPermissions = [
        'group_name'  => 'setting',
        'permissions' => [
            'setting.view',
            'setting.update',
            'database.reset',
        ],
    ];

    public static array $supplierPermissions = [
        'group_name'  => 'supplier',
        'permissions' => [
            'supplier.view',
            'supplier.create',
            'supplier.store',
            'supplier.edit',
            'supplier.update',
            'supplier.delete',
            'supplier.advance',
            'supplier.ledger',
            'supplier.bulk.import',
            'supplier.status',
            'supplier.due.pay',
            'supplier.due.pay.list',
            'supplier.due.pay.delete',
            'supplier.purchase.list',
            'supplier.group',
            'supplier.group.create',
            'supplier.group.edit',
            'supplier.group.delete',
            'supplier.excel.download',
            'supplier.pdf.download',
        ],
    ];

    public static array $customerPermissions = [
        'group_name'  => 'customer',
        'permissions' => [
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.delete',
            'customer.bulk.import',
            'customer.bulk.delete',
            'customer.status',
            'customer.advance',
            'customer.ledger',
            'customer.due.receive.list',
            'customer.due.receive',
            'customer.due.receive.edit',
            'customer.due.receive.delete',
            'customer.sales.list',
            'customer.excel.download',
            'customer.pdf.download',
        ],
    ];
    public static array $customerGroupPermissions = [
        'group_name'  => 'customer group',
        'permissions' => [
            'customer.group.view',
            'customer.group.create',
            'customer.group.edit',
            'customer.group.delete',
        ],
    ];
    public static array $customerAreaPermissions = [
        'group_name'  => 'customer area',
        'permissions' => [
            'customer.area.view',
            'customer.area.create',
            'customer.area.edit',
            'customer.area.delete',
        ],
    ];
    public static array $ingredientPermissions = [
        'group_name'  => 'ingredient',
        'permissions' => [
            'ingredient.view',
            'ingredient.create',
            'ingredient.edit',
            'ingredient.delete',
            'ingredient.status',
            'ingredient.bulk.import',
            'ingredient.barcode.print',
        ],
    ];
    public static array $ingredientUnitTypePermissions = [
        'group_name'  => 'ingredient unit type',
        'permissions' => [
            'ingredient.unit.view',
            'ingredient.unit.create',
            'ingredient.unit.edit',
            'ingredient.unit.delete',
        ],
    ];
    public static array $ingredientCategoryPermissions = [
        'group_name'  => 'ingredient category',
        'permissions' => [
            'ingredient.category.view',
            'ingredient.category.create',
            'ingredient.category.edit',
            'ingredient.category.delete',
        ],
    ];
    public static array $ingredientBrandPermissions = [
        'group_name'  => 'ingredient brand',
        'permissions' => [
            'ingredient.brand.view',
            'ingredient.brand.create',
            'ingredient.brand.edit',
            'ingredient.brand.delete',
        ],
    ];
    public static array $purchasePermissions = [
        'group_name'  => 'purchase',
        'permissions' => [
            'purchase.view',
            'purchase.create',
            'purchase.edit',
            'purchase.delete',
            'purchase.invoice',
            'purchase.excel.download',
            'purchase.pdf.download',
        ],
    ];
    public static array $purchaseReturnPermissions = [
        'group_name'  => 'purchase return',
        'permissions' => [
            'purchase.return.view',
            'purchase.return.create',
            'purchase.return.edit',
            'purchase.return.delete',

        ],
    ];
    public static array $purchaseReturnTypePermissions = [
        'group_name'  => 'purchase return type',
        'permissions' => [
            'purchase.return.type.view',
            'purchase.return.type.create',
            'purchase.return.type.edit',
            'purchase.return.type.delete',

        ],
    ];
    public static array $stockPermissions = [
        'group_name'  => 'stock',
        'permissions' => [
            'stock.view',
            'stock.ledger',
            'stock.reset',
            'stock.excel.download',
            'stock.pdf.download',

        ],
    ];
    public static array $servicePermissions = [
        'group_name'  => 'service',
        'permissions' => [
            'service.view',
            'service.create',
            'service.edit',
            'service.delete',
        ],
    ];
    public static array $serviceCategoryPermissions = [
        'group_name'  => 'service category',
        'permissions' => [
            'service.category.view',
            'service.category.create',
            'service.category.edit',
            'service.category.delete',
        ],
    ];

    public static array $salesPermissions = [
        'group_name'  => 'Sales',
        'permissions' => [
            'pos.view',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.invoice',
            'sales.return',
            'sales.return.list',
            'sales.return.delete',
            'sales.return.excel.download',
            'sales.return.pdf.download',
            'sales.excel.download',
            'sales.pdf.download',
        ],
    ];
    public static array $accountPermissions = [
        'group_name'  => 'Account',
        'permissions' => [
            'cash.flow.view',
            'account.view',
            'account.create',
            'account.edit',
            'account.delete',
            'balance.transfer.view',
            'balance.transfer.create',
            'balance.transfer.edit',
            'balance.transfer.delete',
            'balance.transfer.excel.download',
            'balance.transfer.pdf.download',
            'deposit.withdraw.view',
            'deposit.withdraw.create',
            'deposit.withdraw.edit',
            'deposit.withdraw.delete',
        ],
    ];
    public static array $bankPermissions = [
        'group_name'  => 'bank',
        'permissions' => [
            'bank.view',
            'bank.create',
            'bank.edit',
            'bank.delete',
        ],
    ];
    public static array $quotationPermissions = [
        'group_name'  => 'quotation',
        'permissions' => [
            'quotation.view',
            'quotation.create',
            'quotation.edit',
            'quotation.delete',
            'quotation.excel.download',
            'quotation.pdf.download',
        ],
    ];
    public static array $expensePermissions = [
        'group_name'  => 'expense',
        'permissions' => [
            'expense.view',
            'expense.create',
            'expense.edit',
            'expense.delete',
            'expense.pay',
            'expense.ledger',
            'expense.excel.download',
            'expense.pdf.download',
        ],
    ];
    public static array $expenseTypePermissions = [
        'group_name'  => 'expense type',
        'permissions' => [
            'expense.type.view',
            'expense.type.create',
            'expense.type.edit',
            'expense.type.delete',
            'expense.type.pay',
            'expense.type.ledger',
        ],
    ];
    public static array $expenseSupplierPermissions = [
        'group_name'  => 'expense supplier',
        'permissions' => [
            'expense_supplier.view',
            'expense_supplier.create',
            'expense_supplier.edit',
            'expense_supplier.delete',
            'expense_supplier.due_pay',
            'expense_supplier.advance',
            'expense_supplier.ledger',
            'expense_supplier.excel.download',
            'expense_supplier.pdf.download',
        ],
    ];
    public static array $assetsPermissions = [
        'group_name'  => 'assets',
        'permissions' => [
            'asset.view',
            'asset.create',
            'asset.edit',
            'asset.delete',
            'asset.pay',
            'asset.ledger',
        ],
    ];
    public static array $assetsTypePermissions = [
        'group_name'  => 'assets type',
        'permissions' => [
            'asset.type.view',
            'asset.type.create',
            'asset.type.edit',
            'asset.type.delete',
        ],
    ];
    public static array $employeePermissions = [
        'group_name'  => 'employee',
        'permissions' => [
            'employee.view',
            'employee.create',
            'employee.edit',
            'employee.delete',
            'employee.status',
            'employee.view.payment',
            'employee.pay.salary',
            'employee.pay.advance',
            'employee.edit.salary',
            'employee.delete.salary',
        ],
    ];
    public static array $attendancePermissions = [
        'group_name'  => 'Attendance',
        'permissions' => [
            'attendance.view',
            'attendance.create',
        ],
    ];
    public static array $attendanceSettingPermissions = [
        'group_name'  => 'Attendance Setting',
        'permissions' => [
            'attendance.setting.view',
            'attendance.setting.create',
            'attendance.setting.edit',
            'attendance.setting.delete',
        ],
    ];
    public static array $reportPermissions = [
        'group_name'  => 'report',
        'permissions' => [
            'report.view',
            'report.excel.download',
            'report.pdf.download',
        ],
    ];

    public static array $menuCategoryPermissions = [
        'group_name'  => 'menu category',
        'permissions' => [
            'menu.category.view',
            'menu.category.create',
            'menu.category.edit',
            'menu.category.delete',
        ],
    ];

    public static array $menuItemPermissions = [
        'group_name'  => 'menu item',
        'permissions' => [
            'menu.item.view',
            'menu.item.create',
            'menu.item.edit',
            'menu.item.delete',
        ],
    ];

    public static array $menuAddonPermissions = [
        'group_name'  => 'menu addon',
        'permissions' => [
            'menu.addon.view',
            'menu.addon.create',
            'menu.addon.edit',
            'menu.addon.delete',
        ],
    ];

    public static array $menuComboPermissions = [
        'group_name'  => 'menu combo',
        'permissions' => [
            'menu.combo.view',
            'menu.combo.create',
            'menu.combo.edit',
            'menu.combo.delete',
        ],
    ];

    public static array $menuBranchPermissions = [
        'group_name'  => 'menu branch',
        'permissions' => [
            'menu.branch.pricing',
            'menu.branch.availability',
        ],
    ];
    public static array $membershipPermissions = [
        'group_name'  => 'membership',
        'permissions' => [
            'membership.view',
            'membership.create',
            'membership.edit',
            'membership.manage_points',
            'membership.view_transactions',
        ],
    ];

    // return super admin permission aka 'all permissions'
    private static function getSuperAdminPermissions(): array
    {
        $reflection = new ReflectionClass(__TRAIT__);
        $properties = $reflection->getStaticProperties();

        $permissions = [];
        foreach ($properties as $value) {
            if (is_array($value)) {
                $permissions[] = [
                    'group_name'  => $value['group_name'],
                    'permissions' => (array) $value['permissions'],
                ];
            }
        }

        return $permissions;
    }
}
