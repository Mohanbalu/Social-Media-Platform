# Social Media Platform

This project is a custom-built social media platform developed using web development technologies, including HTML, CSS, JavaScript, and PHP. The backend is managed using phpMyAdmin for database operations.

## Features

### 1. User Management
- **Registration and Login**:
  - Secure user authentication system using PHP and MySQL.
  - Passwords encrypted using hashing for added security.
- **User Profiles**:
  - Customizable user profiles, including profile pictures, bio, and other personal information.

### 2. Social Interactions
- **Post Creation**:
  - Users can create, edit, and delete posts.
- **Like and Comment System**:
  - Enable users to like and comment on posts.
  - Dynamic display of likes and comments using JavaScript and AJAX.

### 3. Newsfeed
- **Dynamic Content Display**:
  - A personalized feed displaying posts from friends and followed accounts.
- **Sorting**:
  - Chronological or popularity-based sorting of posts.

### 4. Friendships/Connections
- **Follow System**:
  - Users can follow or unfollow other users.
- **Friend Requests**:
  - Sending, accepting, and rejecting friend requests.

### 5. Notifications
- **Real-Time Alerts**:
  - Notification system for likes, comments, new followers, and friend requests.

### 6. Search Functionality
- **User and Content Search**:
  - Search for users, posts, or hashtags using optimized queries.

### 7. Responsive Design
- **Mobile and Desktop Compatibility**:
  - Fully responsive UI using CSS and media queries for seamless usage on any device.

## Technology Stack
### Frontend
- HTML: Structure of the platform.
- CSS: Styling the web pages for a modern and clean look.
- JavaScript: Interactive and dynamic functionality.

### Backend
- PHP: Server-side logic for handling requests and database interactions.
- MySQL: Database for storing user profiles, posts, comments, and other platform data.

### Database Management
- Managed using phpMyAdmin for ease of database operations like creating tables and managing data.

## Installation Instructions
1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   ```
2. **Set Up Server**:
   - Use XAMPP or any LAMP/WAMP stack for setting up the server.
3. **Configure Database**:
   - Import the provided `social_media.sql` file into phpMyAdmin to set up the database.
   - Update database credentials in the `config.php` file.
4. **Launch Application**:
   - Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP).
   - Navigate to `http://localhost/<project-folder>` in your web browser.

## Folder Structure
```plaintext
project-root/
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── img/                # Images
├── includes/           # Reusable PHP components
├── config.php          # Database configuration
├── index.php           # Main entry point
├── register.php        # User registration
├── login.php           # User login
├── profile.php         # User profile
├── feed.php            # Newsfeed
├── post.php            # Create/Edit/Delete posts
└── README.md           # Project documentation
```

## Future Enhancements
1. **Real-Time Chat**:
   - Implementing a live messaging system using WebSockets.
2. **Admin Dashboard**:
   - Adding an admin panel to manage users and content.
3. **Advanced Analytics**:
   - Tracking user engagement and platform usage statistics.
4. **API Development**:
   - Providing APIs for mobile app integration.

## Contributing
Contributions are welcome! Please create an issue to report a bug or suggest an enhancement.

## License
This project is licensed under the [MIT License](LICENSE).

---
Let me know if you need further customization or assistance!
