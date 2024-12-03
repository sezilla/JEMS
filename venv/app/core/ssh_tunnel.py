from sshtunnel import SSHTunnelForwarder
from app.core.config import SSH_HOST, SSH_USERNAME, SSH_PRIVATE_KEY_PATH, DB_HOST, DB_PORT

ssh_tunnel = SSHTunnelForwarder(
    (SSH_HOST, 22),
    ssh_username=SSH_USERNAME,
    ssh_private_key=SSH_PRIVATE_KEY_PATH,
    remote_bind_address=(DB_HOST, DB_PORT),
    local_bind_address=("127.0.0.1", 3306)
)

ssh_tunnel.start()
