from fastapi import FastAPI
from app.routes import allocate, project, test

app = FastAPI()

# Include routers
app.include_router(allocate.router)
app.include_router(project.router)
app.include_router(test.router)

@app.on_event("startup")
def startup_event():
    from app.database import ssh_tunnel
    if not ssh_tunnel.is_active:
        raise RuntimeError("SSH tunnel failed to start")

@app.on_event("shutdown")
def shutdown_event():
    from app.database import ssh_tunnel
    ssh_tunnel.stop()
