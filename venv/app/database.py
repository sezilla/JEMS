from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sshtunnel import SSHTunnelForwarder
from app.settings import DATABASE_HOST, DATABASE_PORT, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME, SSH_HOST, SSH_USER, SSH_PRIVATE_KEY_PATH

# SSH Tunnel
ssh_tunnel = SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USER,
    ssh_private_key=SSH_PRIVATE_KEY_PATH,
    remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
    local_bind_address=('127.0.0.1', 3306)
)

ssh_tunnel.start()

DATABASE_URL = f"mysql+pymysql://{DATABASE_USER}:{DATABASE_PASSWORD}@127.0.0.1:3306/{DATABASE_NAME}"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Dependency
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
