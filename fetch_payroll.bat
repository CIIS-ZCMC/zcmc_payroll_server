@echo off
cd /d "D:\Payroll\zcmc_payroll_server"
php artisan fetch:payroll-data
exit
