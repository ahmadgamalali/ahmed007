-- ===================================
-- Full Database Upgrade
-- Adds: Users, Messages with Replies, Dictionary, Newsletter, Influencers
-- ===================================

-- 1. USERS MANAGEMENT TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    bio TEXT,
    avatar_url VARCHAR(500),
    country VARCHAR(100),
    preferred_language ENUM('ar', 'en') DEFAULT 'ar',
    email_verified BOOLEAN DEFAULT FALSE,
    newsletter_subscribed BOOLEAN DEFAULT FALSE,
    last_login DATETIME NULL,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. MESSAGES WITH CONVERSATION THREADS
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS thread_id INT DEFAULT NULL;
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS parent_message_id INT NULL AFTER thread_id;
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS admin_notes TEXT AFTER admin_reply;
ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS read_at DATETIME NULL AFTER replied_at;
ALTER TABLE contact_messages MODIFY COLUMN admin_reply LONGTEXT NULL;

CREATE TABLE IF NOT EXISTS message_conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    thread_id INT UNIQUE NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('open', 'waiting', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    last_message_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_thread (thread_id),
    INDEX idx_status (status),
    INDEX idx_assigned (assigned_to),
    INDEX idx_created (created_at),
    FOREIGN KEY (assigned_to) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ARABIC DICTIONARY TABLE
CREATE TABLE IF NOT EXISTS dictionary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    word_ar VARCHAR(255) UNIQUE NOT NULL,
    pronunciation VARCHAR(255),
    definition_ar LONGTEXT NOT NULL,
    examples LONGTEXT,
    synonyms VARCHAR(500),
    antonyms VARCHAR(500),
    word_type ENUM('noun', 'verb', 'adjective', 'adverb', 'preposition', 'other') DEFAULT 'noun',
    category VARCHAR(100),
    image_url VARCHAR(500),
    video_url VARCHAR(500),
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    usage_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_word (word_ar),
    INDEX idx_category (category),
    INDEX idx_featured (is_featured),
    INDEX idx_level (difficulty_level),
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample dictionary entries
INSERT IGNORE INTO dictionary (word_ar, pronunciation, definition_ar, examples, synonyms, category, difficulty_level, is_featured) VALUES
('الريادة', 'ar-riyādah', 'القدرة على إنشاء مشروع جديد وإدارته بكفاءة والاستعداد لتحمل المخاطر', 'أحمد يسعى لنشر ثقافة الريادة بين الشباب', 'الإدارة، المبادرة', 'أعمال', 'intermediate', TRUE),
('التسويق', 'at-taswīq', 'عملية تروج السلع والخدمات وبيعها للعملاء', 'التسويق الرقمي أصبح ضرورياً في عصرنا الحالي', 'الترويج، البيع', 'أعمال', 'beginner', TRUE),
('الابتكار', 'al-ibtikār', 'إيجاد فكرة جديدة أو طريقة جديدة لعمل شيء ما', 'الابتكار هو مفتاح النجاح في الأعمال', 'الإبداع، التجديد', 'عام', 'intermediate', TRUE);

-- 4. NEWSLETTER SUBSCRIBERS TABLE
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    country VARCHAR(100),
    subscribe_reason VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    confirmation_token VARCHAR(255),
    is_confirmed BOOLEAN DEFAULT FALSE,
    last_received_at DATETIME NULL,
    unsubscribe_reason VARCHAR(255),
    unsubscribed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active),
    INDEX idx_confirmed (is_confirmed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. NEWSLETTER CAMPAIGNS TABLE
CREATE TABLE IF NOT EXISTS newsletter_campaigns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    recipients_count INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    open_count INT DEFAULT 0,
    click_count INT DEFAULT 0,
    created_by INT NOT NULL,
    status ENUM('draft', 'scheduled', 'sent', 'archived') DEFAULT 'draft',
    scheduled_at DATETIME NULL,
    sent_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. INFLUENCERS MANAGEMENT TABLE
CREATE TABLE IF NOT EXISTS influencers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255),
    slug VARCHAR(255) UNIQUE NOT NULL,
    bio TEXT,
    bio_ar TEXT,
    image_url VARCHAR(500),
    category VARCHAR(100),
    category_ar VARCHAR(100),
    specialization VARCHAR(255),
    specialization_ar VARCHAR(255),
    follower_count INT DEFAULT 0,
    engagement_rate DECIMAL(5,2) DEFAULT 0,
    platform VARCHAR(100),
    platform_url VARCHAR(500),
    email VARCHAR(100),
    phone VARCHAR(20),
    country VARCHAR(100),
    city VARCHAR(100),
    rate_per_post DECIMAL(10,2) NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    verification_status ENUM('unverified', 'verified', 'premium') DEFAULT 'unverified',
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    contacts_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_platform (platform),
    INDEX idx_featured (is_featured),
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. INFLUENCER CONTACTS/INQUIRIES
CREATE TABLE IF NOT EXISTS influencer_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    influencer_id INT NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    budget DECIMAL(10,2) NULL,
    proposal_type VARCHAR(100),
    response_status ENUM('pending', 'interested', 'negotiating', 'agreed', 'rejected', 'completed') DEFAULT 'pending',
    admin_notes TEXT,
    replied_at DATETIME NULL,
    completed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_influencer (influencer_id),
    INDEX idx_status (response_status),
    INDEX idx_created (created_at),
    FOREIGN KEY (influencer_id) REFERENCES influencers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. INFLUENCER PORTFOLIO/WORK SAMPLES
CREATE TABLE IF NOT EXISTS influencer_portfolio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    influencer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    video_url VARCHAR(500),
    link VARCHAR(500),
    engagement_metrics TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_influencer (influencer_id),
    FOREIGN KEY (influencer_id) REFERENCES influencers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample influencers
INSERT IGNORE INTO influencers (name, name_ar, slug, bio, bio_ar, follower_count, platform, category, category_ar, specialization, specialization_ar, is_featured, verification_status, status) VALUES
('Mohammad Ali', 'محمد علي', 'mohammad-ali', 'Digital Marketing Expert from Saudi Arabia', 'خبير التسويق الرقمي من السعودية', 150000, 'instagram', 'marketing', 'تسويق', 'Social Media Marketing', 'تسويق وسائل التواصل', TRUE, 'verified', 'active'),
('Fatima Ahmed', 'فاطمة أحمد', 'fatima-ahmed', 'Fashion Blogger & Content Creator', 'مدونة الموضة ومنشئة محتوى', 250000, 'instagram', 'fashion', 'الموضة', 'Fashion & Lifestyle', 'الموضة ونمط الحياة', TRUE, 'verified', 'active'),
('Ali Hassan', 'علي حسن', 'ali-hassan', 'Tech Reviewer & Educational Content Creator', 'خبير تقييم التقنية ومنتج محتوى تعليمي', 180000, 'youtube', 'technology', 'التقنية', 'Tech Reviews & Education', 'مراجعات التقنية والتعليم', FALSE, 'verified', 'active');

-- Alter articles table if needed
ALTER TABLE articles ADD COLUMN IF NOT EXISTS video_url VARCHAR(500) AFTER image_url;
ALTER TABLE articles MODIFY COLUMN content LONGTEXT;

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_articles_author ON articles(author_id);
CREATE INDEX IF NOT EXISTS idx_articles_created ON articles(created_at);
CREATE INDEX IF NOT EXISTS idx_users_created ON users(created_at);

-- Add admin permissions for users management
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS can_manage_users BOOLEAN DEFAULT FALSE;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS can_manage_newsletter BOOLEAN DEFAULT FALSE;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS can_manage_influencers BOOLEAN DEFAULT FALSE;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS can_manage_dictionary BOOLEAN DEFAULT FALSE;

UPDATE admin_users SET can_manage_users=TRUE, can_manage_newsletter=TRUE, can_manage_influencers=TRUE, can_manage_dictionary=TRUE WHERE role='admin';

-- ===================================
-- Views for easy data retrieval
-- ===================================

CREATE OR REPLACE VIEW v_active_subscribers AS
SELECT email, full_name, country, is_confirmed, created_at
FROM newsletter_subscribers
WHERE is_active = TRUE AND is_confirmed = TRUE
ORDER BY created_at DESC;

CREATE OR REPLACE VIEW v_influencer_summary AS
SELECT 
    i.id,
    i.name_ar as name,
    i.category_ar as category,
    i.platform,
    i.follower_count,
    i.engagement_rate,
    COUNT(DISTINCT ic.id) as contact_count,
    COUNT(DISTINCT ip.id) as portfolio_count,
    i.status
FROM influencers i
LEFT JOIN influencer_contacts ic ON i.id = ic.influencer_id
LEFT JOIN influencer_portfolio ip ON i.id = ip.influencer_id
GROUP BY i.id
ORDER BY i.is_featured DESC, i.follower_count DESC;

-- ===================================
-- Stored Procedures for common operations
-- ===================================

DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS sp_create_conversation(
    IN p_thread_id INT,
    IN p_email VARCHAR(100),
    IN p_name VARCHAR(100),
    IN p_subject VARCHAR(255),
    OUT p_success BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    INSERT INTO message_conversations (thread_id, contact_email, contact_name, subject)
    VALUES (p_thread_id, p_email, p_name, p_subject);
    
    SET p_success = TRUE;
    COMMIT;
END$$

CREATE PROCEDURE IF NOT EXISTS sp_get_conversation_thread(
    IN p_thread_id INT
)
BEGIN
    SELECT 
        m.id,
        m.name,
        m.email,
        m.message,
        m.admin_reply,
        m.admin_notes,
        u.full_name as replied_by_name,
        m.replied_at,
        m.read_at,
        m.status
    FROM contact_messages m
    LEFT JOIN admin_users u ON m.replied_by = u.id
    WHERE m.thread_id = p_thread_id OR m.id = p_thread_id
    ORDER BY m.created_at ASC;
END$$

DELIMITER ;
