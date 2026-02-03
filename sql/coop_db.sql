-- Create Coop DB
CREATE DATABASE IF NOT EXISTS coop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coop_db;

-- Roles table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Permissions table
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User roles (many-to-many)
CREATE TABLE user_roles (
    user_id INT NOT NULL,  -- FK to people_db.users, no direct FK
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Role permissions (many-to-many)
CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Anggota table (members)
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- FK to people_db.users
    status_keanggotaan ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    nomor_anggota VARCHAR(20) UNIQUE NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE CASCADE,
    INDEX idx_anggota_user (user_id)
);

-- Pengurus table (management)
CREATE TABLE pengurus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- FK to people_db.users
    jabatan VARCHAR(100) NOT NULL,
    periode_start DATE NOT NULL,
    periode_end DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE CASCADE,
    INDEX idx_pengurus_user (user_id)
);

-- Pengawas table (supervisors)
CREATE TABLE pengawas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- FK to people_db.users
    jabatan VARCHAR(100) NOT NULL,
    periode_start DATE NOT NULL,
    periode_end DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE CASCADE,
    INDEX idx_pengawas_user (user_id)
);

-- Simpanan types
CREATE TABLE simpanan_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    interest_rate DECIMAL(5,2) DEFAULT 0.00,  -- annual rate
    minimum_balance DECIMAL(15,2) DEFAULT 0.00,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Simpanan transactions
CREATE TABLE simpanan_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT NOT NULL,
    type_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    transaction_type ENUM('deposit', 'withdraw') NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    approved_by INT,  -- FK to people_db.users if needed
    FOREIGN KEY (anggota_id) REFERENCES anggota(id),
    FOREIGN KEY (type_id) REFERENCES simpanan_types(id),
    FOREIGN KEY (approved_by) REFERENCES people_db.users(id) ON DELETE SET NULL,
    INDEX idx_simpanan_anggota (anggota_id),
    INDEX idx_simpanan_date (transaction_date)
);

-- Pinjaman (loans)
CREATE TABLE pinjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,  -- annual rate
    term_months INT NOT NULL,
    status ENUM('pending', 'approved', 'active', 'paid', 'rejected') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    approved_by INT,  -- FK to people_db.users
    disbursed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id),
    FOREIGN KEY (approved_by) REFERENCES people_db.users(id) ON DELETE SET NULL,
    INDEX idx_pinjaman_anggota (anggota_id),
    INDEX idx_pinjaman_status (status)
);

-- Pinjaman angsuran (loan installments)
CREATE TABLE pinjaman_angsuran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pinjaman_id INT NOT NULL,
    installment_number INT NOT NULL,
    due_date DATE NOT NULL,
    principal_amount DECIMAL(15,2) NOT NULL,
    interest_amount DECIMAL(15,2) NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0.00,
    paid_at TIMESTAMP NULL,
    status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
    penalty DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (pinjaman_id) REFERENCES pinjaman(id)
);

-- Chart of accounts
CREATE TABLE chart_of_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    parent_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id)
);

-- Journal entries
CREATE TABLE journal_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_date DATE NOT NULL,
    description TEXT NOT NULL,
    reference_number VARCHAR(50),
    status ENUM('draft', 'posted') DEFAULT 'draft',
    posted_by INT,  -- FK to people_db.users
    posted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE journal_entries
    ADD CONSTRAINT fk_journal_posted_by FOREIGN KEY (posted_by) REFERENCES people_db.users(id) ON DELETE SET NULL;

-- Journal entry details
CREATE TABLE journal_entry_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_entry_id INT NOT NULL,
    account_id INT NOT NULL,
    debit DECIMAL(15,2) DEFAULT 0.00,
    credit DECIMAL(15,2) DEFAULT 0.00,
    description TEXT,
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id),
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id)
);

-- General ledger
CREATE TABLE general_ledger (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    period DATE NOT NULL,  -- e.g., YYYY-MM-01
    beginning_balance DECIMAL(15,2) DEFAULT 0.00,
    debit_total DECIMAL(15,2) DEFAULT 0.00,
    credit_total DECIMAL(15,2) DEFAULT 0.00,
    ending_balance DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id),
    UNIQUE KEY (account_id, period)
);

-- SHU distributions
CREATE TABLE shu_distributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year YEAR NOT NULL,
    total_shu DECIMAL(15,2) NOT NULL,
    distributed_at TIMESTAMP NULL,
    status ENUM('calculated', 'distributed') DEFAULT 'calculated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Member SHU
CREATE TABLE member_shu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT NOT NULL,
    shu_distribution_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    paid BOOLEAN DEFAULT FALSE,
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (anggota_id) REFERENCES anggota(id),
    FOREIGN KEY (shu_distribution_id) REFERENCES shu_distributions(id)
);

-- Products (for e-commerce)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    category VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,  -- Could be anggota_id or external
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid'
);

-- Order details
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Agent sales (for resellers)
CREATE TABLE agent_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,  -- FK to anggota(id) if agent is member
    order_id INT NOT NULL,
    commission DECIMAL(15,2) NOT NULL,
    approved BOOLEAN DEFAULT FALSE,
    approved_by INT,  -- FK to people_db.users
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (agent_id) REFERENCES anggota(id),
    FOREIGN KEY (approved_by) REFERENCES people_db.users(id) ON DELETE SET NULL
);

-- Votes (voting sessions)
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agenda VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    created_by INT,  -- FK to people_db.users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE votes
    ADD CONSTRAINT fk_votes_created_by FOREIGN KEY (created_by) REFERENCES people_db.users(id) ON DELETE SET NULL;

-- Vote ballots
CREATE TABLE vote_ballots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vote_id INT NOT NULL,
    user_id INT NOT NULL,  -- FK to people_db.users
    choice VARCHAR(100) NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vote_id) REFERENCES votes(id),
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE CASCADE,
    INDEX idx_vote_ballots_vote_user (vote_id, user_id)
);

-- Audit logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,  -- FK to people_db.users
    action VARCHAR(255) NOT NULL,
    details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE SET NULL
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- FK to people_db.users
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'error') DEFAULT 'info',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES people_db.users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_read (user_id, read_at)
);

-- Configs (system settings)
CREATE TABLE configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('super_admin', 'Super administrator with all access'),
('admin', 'Administrator/Pengurus'),
('pengawas', 'Pengawas with read/approve access'),
('anggota', 'Regular member'),
('calon_anggota', 'Prospective member');

-- Insert default permissions
INSERT INTO permissions (name, description) VALUES
('view_users', 'View user list'),
('create_users', 'Create new users'),
('edit_users', 'Edit user information'),
('delete_users', 'Delete users'),
('view_members', 'View members'),
('manage_members', 'Manage member data'),
('view_savings', 'View savings transactions'),
('manage_savings', 'Manage savings'),
('view_loans', 'View loan applications'),
('manage_loans', 'Manage loans'),
('view_accounts', 'View chart of accounts'),
('manage_accounts', 'Manage accounting'),
('view_reports', 'View reports'),
('generate_reports', 'Generate financial reports'),
('vote', 'Participate in voting'),
('manage_votes', 'Manage voting sessions'),
('view_audit', 'View audit logs'),
('admin_access', 'Full administrative access');

-- Insert default configs
INSERT INTO configs (key_name, value, description) VALUES
('coop_name', 'Koperasi Simpan Pinjam', 'Nama koperasi'),
('interest_rate_savings', '3.5', 'Suku bunga simpanan tahunan (%)'),
('interest_rate_loans', '12.0', 'Suku bunga pinjaman tahunan (%)'),
('penalty_rate', '2.0', 'Denda keterlambatan (%) per hari'),
('shu_distribution_ratio', '70', 'Persentase SHU untuk anggota (%)');

-- Insert default chart of accounts (basic)
INSERT INTO chart_of_accounts (code, name, type) VALUES
('1000', 'Kas', 'asset'),
('1100', 'Bank', 'asset'),
('2000', 'Simpanan Anggota', 'liability'),
('2100', 'Pinjaman Anggota', 'asset'),
('3000', 'Modal', 'equity'),
('4000', 'Pendapatan Bunga', 'revenue'),
('5000', 'Beban Bunga', 'expense'),
('5100', 'Beban Operasional', 'expense');
