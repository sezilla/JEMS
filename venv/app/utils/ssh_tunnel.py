from sshtunnel import SSHTunnelForwarder
from app.config import Config

SSH_TUNNEL = None

def start_tunnel():
    global SSH_TUNNEL
    SSH_TUNNEL = SSHTunnelForwarder(
        (Config.SSH_HOST, 22),
        ssh_username=Config.SSH_USER,
        ssh_private_key=Config.SSH_PRIVATE_KEY_PATH,
        remote_bind_address=(Config.DATABASE_HOST, Config.DATABASE_PORT),
        local_bind_address=("127.0.0.1", 3306),
    )
    SSH_TUNNEL.start()

def stop_tunnel():
    if SSH_TUNNEL:
        SSH_TUNNEL.stop()
