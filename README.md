# Wasalni App

Wasalni is a backend system developed using Laravel to support a Flutter-based mobile application. The platform facilitates ride-sharing services by connecting regular users with service providers, managed through an administrative interface.

## 🚀 Features

-   **User Roles**:

    -   **Regular User**: Can browse and book available trips.
    -   **Service Provider**: Can accept or reject trip bookings.
    -   **Administrator**: Manages user roles and approves service provider applications.

-   **Trip Management**:

    -   View available trips with detailed information.
    -   Book trips and manage bookings.
    -   Mark trips and service providers as favorites for quick access.

-   **Notifications**:
    -   Real-time updates on booking statuses.
    -   Alerts for trip changes and administrative actions.

## 🛠️ Technologies Used

-   **Backend Framework**: Laravel (PHP)
-   **Database**: MySQL
-   **Authentication**: Laravel Sanctum
-   **Notifications**: Firebase Cloud Messaging (FCM)
-   **API Documentation**: Swagger/OpenAPI

## 📂 Project Structure

-   `app/`: Core application logic.
-   `routes/`: API route definitions.
-   `resources/`: Views and language files.
-   `database/`: Migrations and seeders.
-   `public/`: Public assets.

## 📦 Installation

### Steps to Run the Project:

1. **Clone the Repository**:

    ```bash
    git clone https://github.com/MohammedAlmostfa/Wasalni-Project.git
    ```

2. **Navigate to the Project Directory**:

    ```sh
    cd Wasalni-Project
    ```

3. **Install Dependencies**:

    ```sh
    composer install
    ```

4. **Create Environment File**:

    ```sh
    cp .env.example .env
    ```

5. **Update the .env File** with your database configuration (MySQL credentials, database name, etc.).

6. **Generate Application Key**:

    ```sh
    php artisan key:generate
    ```

7. **Generate JWT Secret Key**:

    ```sh
    php artisan jwt:secret
    ```

8. **Run Migrations**:

    ```sh
    php artisan migrate
    ```

9. **Seed the Database**:

    ```sh
    php artisan db:seed
    ```

10. **Run the Job Queue**:

    ```sh
    php artisan queue:work
    ```

11. **Run the Application**:
    ```sh
    php artisan serve
    ```

---

### **Important Notes**:

-   Pay attention to the validation instructions in the request file for each operation you want to perform.
-   Test your work manually using Postman or HTTP.
-   You are welcome to create additional files.
-   Follow best practices to produce clean and professional results.

---

## Credits

-   [Mohammed Almostfa](https://github.com/MohammedAlmostfa)

## Contact

For any inquiries or support, please contact:

-   **Phone**: +963991851269
-   **GitHub**: [Mohammed Almostfa](https://github.com/MohammedAlmostfa)
-   **LinkedIn**: [Mohammed Almostfa](https://www.linkedin.com/in/mohammed-almostfa-63b3a7240/)

---

Thank you for using our services.
