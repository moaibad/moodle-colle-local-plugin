
# Moodle WebService for Collaborative Learning (Colle)

Fitur Quiz adalah bagian dari platform Colle yang dikembangkan dengan tujuan meningkatkan pembelajaran dan evaluasi di lingkungan akademik, melibatkan mahasiswa, dosen, dan praktisi. Fitur ini mendorong partisipasi aktif dari semua pihak yang terlibat, baik mahasiswa yang ingin menguji pemahaman mereka, dosen yang menyediakan evaluasi melalui soal soal, maupun praktisi yang ingin menawarkan studi kasus berbasis pengalaman yang praktis. Dengan melibatkan semua pihak sebagai pembuat konten utama, fitur ini bertujuan untuk memberikan metode evaluasi yang interaktif.


## Arsitektur Sistem

Dari segi teknologi, fitur Quiz menggunakan Moodle sebagai platform, MySQL sebagai database, dan PHP sebagai bahasa pemrograman. Fitur Quiz diimplementasikan sebagai local plugin yang berjalan di atas Moodle. Plugin ini dibuat untuk memenuhi fungsi-fungsi yang dibutuhkan oleh aplikasi Colle yang belum tersedia di Moodle, sehingga memerlukan kustomisasi.
## Prasyarat

- Moodle 4.3 atau versi yang lebih baru sudah terinstal pada komputer. Untuk instalasi Moodle, Anda bisa mengikuti panduan berikut: [Instalasi Moodle - Panduan YouTube](https://www.youtube.com/watch?v=O4BU14qbTag&t)

- Aktifkan REST Service pada Moodle dengan mengikuti panduan berikut: [Aktivasi REST Service - Panduan YouTube](https://www.youtube.com/watch?v=Ud139zt2s8c)
## Panduan Instalasi

1. Clone repository plugin Moodle Colle:
    ```bash
    git clone https://github.com/moaibad/moodle-colle-local-plugin.git
    ```

2. Pindahkan hasil clone ke direktori `/local/` pada proyek Moodle Anda:
    ```bash
    mv moodle-colle-local-plugin /path/to/your/moodle/local/colle
    ```

3. Aktifkan plugin lokal melalui halaman admin Moodle:
    - Login ke Moodle menggunakan akun admin.
    - Setelah login, Moodle akan mendeteksi plugin baru dan menampilkan halaman instalasi.
    - Ikuti langkah-langkah instalasi hingga selesai.

4. Tambahkan fungsi-fungsi berikut ke dalam web service:
- core_course_get_contents
- core_course_get_course_module_by_instance
- core_course_get_courses
- core_course_get_recent_courses
- core_enrol_get_enrolled_users
- core_enrol_get_users_courses
- core_role_assign_roles
- core_user_create_users
- core_user_get_users_by_field
- enrol_manual_enrol_users
- enrol_self_enrol_user
- enrol_self_get_instance_info
- local_colle_create_course
- local_colle_create_quiz
- local_colle_get_all_quiz
- local_colle_get_all_user_best_grades
- local_colle_get_context_id
- local_colle_get_quiz
- local_colle_get_user_best_grades_by_quiz
- mod_quiz_get_user_attempts
    
## Dokumentasi API

Aplikasi Colle menggunakan service REST API dari Moodle. Beberapa service tersebut berasal dari service bawaan Moodle, sementara lainnya dibungkus dalam local plugin yang telah kami buat. Berikut adalah service-service yang digunakan pada aplikasi Colle:

### **1. Built-in Service**

### `core_course_get_contents`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_course_get_contents`
- **Description**: Get course contents

### `core_course_get_course_module_by_instance`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_course_get_course_module_by_instance&module=quiz&instance={instance}`
- **Description**: Return information about a given module name and instance id

### `core_course_get_courses`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_course_get_courses`
- **Description**: Return information about a given module name and instance id

### `core_course_get_recent_courses`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_course_get_recent_courses`
- **Description**: List of courses a user has accessed most recently.

### `core_enrol_get_enrolled_users`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_enrol_get_enrolled_users&courseid={courseid}`
- **Description**: Get enrolled users by course id.

### `core_enrol_get_users_courses`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_enrol_get_users_courses&userid={userid}`
- **Description**: Get the list of courses where a user is enrolled in.

### `core_role_assign_roles`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_role_assign_roles&assignments[0][roleid]={roleid}&assignments[0][userid]={userid}&assignments[0][contextid]={contextid}&assignments[0][contextlevel]={contextlevel}`
- **Description**: Manual role assignments.

### `core_user_create_users`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_user_create_users&users[0][username]={username}&users[0][password]={password}&users[0][firstname]={firstname}&users[0][lastname]={lastname}&users[0][email]={email}`
- **Description**: Create users.

### `core_user_get_users_by_field`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=core_user_get_users_by_field&field={field}&values[0]={values}`
- **Description**: Retrieve users' information for a specified unique field.

### `enrol_manual_enrol_users`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=enrol_manual_enrol_users&enrolments[0][roleid]={roleid}&enrolments[0][userid]={userid}&enrolments[0][courseid]={courseid}`
- **Description**: Manual enrol users.

### `mod_quiz_get_user_attempts`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=mod_quiz_get_user_attempts&quizid={quizid}&userid={userid}`
- **Description**: Return a list of attempts for the given quiz and user.


### **2. Custom Service**

### `local_colle_create_course`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_create_course&fullname={fullname}&shortname={shortname}&enrolmentkey={enrolmentkey}&summary={summary}&userid={userid}`
- **Description**: Create a new course and set enrolment key.

### `local_colle_create_quiz`
- **Method**: POST
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_create_quiz&courseid={courseid}&quizname={quizname}&intro={intro}&userid={userid}`
- **Description**: Create a new quiz in course.

### `local_colle_get_all_quiz`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_get_all_quiz`
- **Description**: Return all quiz.

### `local_colle_get_all_user_best_grades`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_get_all_user_best_grades&userid={userid}`
- **Description**: Return a list of attempts for the given user.

### `local_colle_get_context_id`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_get_context_id&courseid={courseid}&quizid={quizid}`
- **Description**: Return context id from module.

### `local_colle_get_quiz`
- **Method**: GET
- **Endpoint**: `http://{base_url}/webservice/rest/server.php?moodlewsrestformat=json&wstoken={token}&wsfunction=local_colle_get_quiz&userid={userid}`
- **Description**: Return created quiz by user.



## Authors

- Mohammad Fathul'ibad
- Fardan Al Jihad
- Annisa Dinda Gantini
- Maolana Firmansyah
- Salma Edyna Putri

