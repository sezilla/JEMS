from fastapi import FastAPI
from app.routes.health import router as health_router
from app.routes.allocate import router as allocate_router
from app.routes.projects import router as projects_router

def include_routes(app: FastAPI):
    app.include_router(health_router, prefix="/health", tags=["Health"])
    app.include_router(allocate_router, prefix="/allocate", tags=["Allocate"])
    app.include_router(projects_router, prefix="/projects", tags=["Projects"])
