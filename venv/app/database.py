# app/database.py

import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from sshtunnel import SSHTunnelForwarder
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Database connection details from the .env file
DATABASE_HOST = os.getenv("DB_HOST")
DATABASE_PORT = int(os.getenv("DB_PORT", "3306"))
DATABASE_NAME = os.getenv("DB_DATABASE")
DATABASE_USER = os.getenv("DB_USERNAME")
DATABASE_PASSWORD = os.getenv("DB_PASSWORD")
SSH_HOST = os.getenv("SSH_HOST")
SSH_USER = os.getenv("SSH_USERNAME")
SSH_PRIVATE_KEY_PATH = "/etc/secrets/jem"  # Replace with Render's secret file path

# SSH Tunnel for secure connection to the remote database
ssh_tunnel = SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USER,
    ssh_private_key=SSH_PRIVATE_KEY_PATH,
    remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
    local_bind_address=('127.0.0.1', 3306)
)

ssh_tunnel.start()

# Database URL for SQLAlchemy
DATABASE_URL = f"mysql+pymysql://{DATABASE_USER}:{DATABASE_PASSWORD}@127.0.0.1:3306/{DATABASE_NAME}"

# Create SQLAlchemy engine and sessionmaker
engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()
