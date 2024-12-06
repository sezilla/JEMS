from sshtunnel import SSHTunnelForwarder
from app.config import SSH_HOST, SSH_USER, SSH_PRIVATE_KEY_PATH, DATABASE_HOST, DATABASE_PORT

ssh_tunnel = SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USER,
    ssh_private_key=SSH_PRIVATE_KEY_PATH,
    remote_bind_address=(DATABASE_HOST, DATABASE_PORT),
    local_bind_address=('127.0.0.1', 3306)
)
