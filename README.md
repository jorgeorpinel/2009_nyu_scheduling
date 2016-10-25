This file was created in 2016 to document this old project.  
This is a CakePHP web application.

## Appointment Scheduling System
For NYU Steinhardt MPAP department  
&copy; 2009 [Jorge Orpinel](http://jorge.orpinel.com)

## Requires

**Back-end**
* [CakePHP 1.2.4](http://book.cakephp.org/1.2/en/) core in `cake/` ([download](https://github.com/cakephp/cakephp/releases/tag/1.2.4))
* [PHPExcelReader](http://sourceforge.net/projects/phpexcelreader) from 2008 in `vendors/excel/`

**Front-end**
* Prototype.js 1.5.1

## Usage

You can import `2009_12_20_dump.sql` (parent dir).

### Students

A sample student creds are **N16176849**:_student_. Students can view their courses (`/my_courses`) and their **semester schedule** (`/students/schedule/{UserId}`).

### Admins

The default sys admin credentials are **admin**:_admin_. Admins can **manage or import from XML** courses (`/admin/courses`), faculty member records (`/admin/faculty`), and student profiles (`/admin/students`); They may also **manage users** (`/admin/users`).

For a full list of available processes, their URLs and controller method mapping, see [2009 NYU - Scheduling - Processes.xls](https://1drv.ms/x/s!AgNFd02XYIc1hQzv5AWWhdMtGlM3).
