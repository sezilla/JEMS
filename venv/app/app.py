import app
from app.utils.ssh_tunnel import start_tunnel, stop_tunnel
from app.db import initialize_database

@app.on_event("startup")
def startup_event():
    start_tunnel()
    initialize_database()  # Initialize the database connection after starting the tunnel

@app.on_event("shutdown")
def shutdown_event():
    stop_tunnel()
