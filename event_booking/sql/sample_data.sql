-- Insert Sample Users (1 admin, 2 regular users)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', 'passwordadmin', 'admin'),
('John Doe', 'john@example.com', 'passwordjohn', 'user'),
('Jane Smith', 'jane@example.com', '$passwordjane', 'user');

-- Insert Sample Events
INSERT INTO events (title, description, date, venue, available_seats) VALUES 
('Music Concert', 'A live music concert with top artists.', '2025-03-10', 'City Hall', 100),
('Tech Conference', 'Technology and AI trends discussion.', '2025-04-15', 'Tech Park', 50),
('Art Exhibition', 'Display of modern and classical art.', '2025-05-05', 'Art Gallery', 30);

-- Insert Sample Bookings
INSERT INTO bookings (user_id, event_id) VALUES 
(2, 1), -- John Doe booked the Music Concert
(3, 2); -- Jane Smith booked the Tech Conference
