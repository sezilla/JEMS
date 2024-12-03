import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from sshtunnel import SSHTunnelForwarder

# Load environment variables
DATABASE_HOST = os.getenv("DB_HOST")
DATABASE_PORT = int(os.getenv("DB_PORT", "3306"))
DATABASE_NAME = os.getenv("DB_DATABASE")
DATABASE_USER = os.getenv("DB_USERNAME")
DATABASE_PASSWORD = os.getenv("DB_PASSWORD")
SSH_HOST = os.getenv("SSH_HOST")
SSH_USER = os.getenv("SSH_USERNAME")
SSH_PRIVATE_KEY_PATH = "/etc/secrets/jem"

# SSH Tunnel
ssh_tunnel = SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USER,
    ssh_private_key=SSH_PRIVATE_KEY_PATH,
    remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
    local_bind_address=("127.0.0.1", 3306),
)
ssh_tunnel.start()

# Database URL
DATABASE_URL = f"mysql+pymysql://{DATABASE_USER}:{DATABASE_PASSWORD}@127.0.0.1:3306/{DATABASE_NAME}"

# SQLAlchemy setup
engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Dependency
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
