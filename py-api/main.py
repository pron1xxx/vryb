from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import uvicorn
import chardet
from test_parser import TestParser
import os
import logging
import traceback

# Настройка логирования
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

app = FastAPI(title="Test Parser API", description="API для парсинга тестов из TXT файла")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

UPLOAD_DIR = "uploads"
os.makedirs(UPLOAD_DIR, exist_ok=True)

parser = TestParser()

@app.post("/api/parse-test/")
async def parse_test(file: UploadFile = File(...)):
    logger.info(f"📥 Получен запрос с файлом: {file.filename}")
    
    if not file.filename.endswith('.txt'):
        raise HTTPException(400, "Можно загружать только TXT файлы")
    
    try:
        contents = await file.read()
        
        if len(contents) > 1024 * 1024:
            raise HTTPException(400, "Файл слишком большой (макс. 1 МБ)")
        
        # Определяем кодировку
        encoding_info = chardet.detect(contents)
        encoding = encoding_info['encoding'] or 'utf-8'
        
        # Декодируем текст
        try:
            text = contents.decode(encoding)
        except UnicodeDecodeError:
            text = None
            for enc in ['utf-8', 'windows-1251', 'cp1252']:
                try:
                    text = contents.decode(enc)
                    break
                except UnicodeDecodeError:
                    continue
            if text is None:
                text = contents.decode('utf-8', errors='ignore')
        
        # Парсим тест
        test_data = parser.parse_test_file(text)
        
        return {
            "success": True,
            "data": test_data
        }   
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Ошибка обработки: {str(e)}")
        raise HTTPException(500, f"Ошибка обработки: {str(e)}")

@app.get("/")
def root():
    return {
        "message": "Test Parser API is ready",
        "endpoints": {
            "POST /api/parse-test/": "Загрузить и распарсить файл с тестом",
        }
    }

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)