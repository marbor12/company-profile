-- Create review_approval table for pending reviews
CREATE TABLE IF NOT EXISTS review_approval (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    rate INT NOT NULL CHECK (rate >= 1 AND rate <= 5),
    review TEXT NOT NULL,
    id_event INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    reviewed_by INT,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (id_event) REFERENCES event(id) ON DELETE SET NULL
);

-- Add status column to existing review table if not exists
ALTER TABLE review ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active';
ALTER TABLE review ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL;
ALTER TABLE review ADD COLUMN IF NOT EXISTS approved_by INT NULL;
