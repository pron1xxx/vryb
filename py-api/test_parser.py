import re
from typing import List, Dict, Any

class TestParser():
    
    @staticmethod
    def parse_test_file(content: str) -> Dict[str, Any]:
        
        lines = content.strip().split('\n')
        test_data = {
            "test_name": "Без названия",
            "questions": []
        }
        
        current_question = None
        
        # ИСПРАВЛЕНО: line in lines, а не lines in line
        for line in lines:
            line = line.strip()
            if not line:
                continue
            
            # ИСПРАВЛЕНО: startswith (было startwith)
            if line.startswith('Название теста:'):
                test_data['test_name'] = line.replace('Название теста:', '').strip()
                
            elif line.startswith("Вопрос:"):
                if current_question:
                    test_data["questions"].append(current_question)
                    
                current_question = {
                    "question": line.replace('Вопрос:', '').strip(),  # ИСПРАВЛЕНО: русский "Вопрос:"
                    "answers": [],
                    "correct_answer": None
                }
                
            elif line.startswith("Ответ:") and current_question: 
                current_question["answers"].append(line.replace("Ответ:", '').strip())
                
            elif line.startswith("Правильный ответ:") and current_question:
                current_question["correct_answer"] = line.replace("Правильный ответ:", '').strip()
        
        if current_question:
            test_data["questions"].append(current_question)
            
        return test_data