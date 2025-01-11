-- Sample users
INSERT INTO users (username, email, password, full_name, profile_picture) VALUES
('johndoe', 'john@example.com', 'password123', 'John Doe', 'profile_default.png'),
('janedoe', 'jane@example.com', 'password456', 'Jane Doe', 'profile_default.png');

-- Sample job listings
INSERT INTO jobs (user_id, title, description, budget) VALUES
(1, 'Music Producer Needed', 'Looking for a producer to create a beat for a new album.', 500.00),
(2, 'Sound Engineer for Concert', 'Need a sound engineer for a live concert in New York.', 700.00);

-- Sample messages
INSERT INTO messages (sender_id, receiver_id, message_text) VALUES
(1, 2, 'Hi, I saw your job post. I am interested!'),
(2, 1, 'Thank you for applying! Let\'s discuss the details soon.');
