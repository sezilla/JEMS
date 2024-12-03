from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from .config import DATABASE_HOST, DATABASE_PORT, DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD
from sshtunnel import SSHTunnelForwarder

# SSH Tunnel
ssh_tunnel = SSHTunnelForwarder(
    (DATABASE_HOST, 22),
    ssh_username=os.getenv("SSH_USERNAME"),
    ssh_private_key=os.getenv("SSH_PRIVATE_KEY_PATH"),
    remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
    local_bind_address=('127.0.0.1', 3306)
)

ssh_tunnel.start()

# Database URL
DATABASE_URL = f"mysql+pymysql://{DATABASE_USER}:{DATABASE_PASSWORD}@127.0.0.1:3306/{DATABASE_NAME}"

# SQLAlchemy setup
engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
