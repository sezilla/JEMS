from app import app

@app.on_event("startup")
def startup_event():
    from app.utils.ssh_tunnel import start_tunnel
    start_tunnel()

@app.on_event("shutdown")
def shutdown_event():
    from app.utils.ssh_tunnel import stop_tunnel
    stop_tunnel()
