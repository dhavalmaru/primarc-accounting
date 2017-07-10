INSERT INTO `acc_user_role_master` (`id`, `role`, `description`, `status`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'Admin', 'Admin', 'approved', 1, 16, '2017-05-23 00:00:00', 16, '2017-05-23 07:49:58'),
(2, 'View', 'View', 'approved', 1, 16, '2017-05-24 00:00:00', 16, '2017-05-25 02:36:09'),
(3, 'Update', 'Update', 'approved', 1, 16, '2017-05-24 15:15:50', 16, '2017-05-25 02:36:49'),
(4, 'Approve', 'Approve', 'approved', 1, 16, '2017-05-25 08:07:30', 16, '2017-05-25 02:37:30');

INSERT INTO `acc_user_roles` (`user_id`, `role_id`, `status`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(16, 1, 'approved', 1, 16, '2017-07-10 10:50:24', 16, '2017-07-10 10:50:24');