<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Квиз по подбору дымососа и рукавов</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    * {
      font-family: 'Inter', sans-serif;
    }
    
    body {
      background: #0f1419;
      color: #e5e7eb;
    }
    
    /* Grid background */
    .grid-bg {
      background-image: 
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
      background-size: 50px 50px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 0;
    }
    
    .content-wrapper {
      position: relative;
      z-index: 1;
    }
    
    /* Industrial card */
    .industrial-card {
      background: #1a1f29;
      border: 1px solid #2d3748;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }
    
    /* Progress bar */
    .progress-track {
      background: #2d3748;
      position: relative;
      overflow: hidden;
    }
    
    .progress-fill {
      background: linear-gradient(90deg, #ef4444, #dc2626);
      transition: width 0.4s ease;
      box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }
    
    /* Radio buttons */
    .radio-option {
      background: #1a1f29;
      border: 2px solid #2d3748;
      transition: all 0.2s ease;
      position: relative;
    }
    
    .radio-option:hover {
      border-color: #4b5563;
      background: #1f2937;
    }
    
    .radio-option.selected {
      border-color: #ef4444;
      background: rgba(239, 68, 68, 0.05);
    }
    
    .radio-dot {
      width: 20px;
      height: 20px;
      border: 2px solid #4b5563;
      border-radius: 50%;
      position: relative;
      transition: all 0.2s ease;
    }
    
    .radio-option.selected .radio-dot {
      border-color: #ef4444;
    }
    
    .radio-dot::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      width: 10px;
      height: 10px;
      background: #ef4444;
      border-radius: 50%;
      transition: transform 0.2s ease;
    }
    
    .radio-option.selected .radio-dot::after {
      transform: translate(-50%, -50%) scale(1);
    }
    
    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(239, 68, 68, 0.4);
    }
    
    .btn-secondary {
      background: #2d3748;
      border: 1px solid #4b5563;
      transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
      background: #374151;
      border-color: #6b7280;
    }
    
    /* Step indicators */
    .step-indicator {
      width: 40px;
      height: 40px;
      border: 2px solid #2d3748;
      background: #1a1f29;
      transition: all 0.3s ease;
    }
    
    .step-indicator.active {
      border-color: #ef4444;
      background: rgba(239, 68, 68, 0.1);
      box-shadow: 0 0 12px rgba(239, 68, 68, 0.3);
    }
    
    .step-indicator.completed {
      border-color: #10b981;
      background: rgba(16, 185, 129, 0.1);
    }
    
    /* Animations */
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }
    
    .animate-slide-in {
      animation: slideIn 0.3s ease-out;
    }
    
    /* Result cards */
    .result-card {
      background: #1a1f29;
      border: 2px solid #2d3748;
      transition: all 0.3s ease;
    }
    
    .result-card:hover {
      border-color: #4b5563;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }
    
    .result-card.main {
      border-color: #ef4444;
      box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
    }
    
    /* Badge */
    .badge {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid #ef4444;
      color: #ef4444;
    }
    
    .badge.alt {
      background: rgba(59, 130, 246, 0.1);
      border: 1px solid #3b82f6;
      color: #3b82f6;
    }
    
    /* Accent line */
    .accent-line {
      height: 3px;
      background: linear-gradient(90deg, #ef4444, transparent);
    }
    
    /* Input fields */
    input[type="text"],
    input[type="tel"],
    input[type="email"] {
      background: #1a1f29;
      border: 2px solid #2d3748;
      color: #e5e7eb;
      transition: all 0.2s ease;
    }
    
    input[type="text"]:focus,
    input[type="tel"]:focus,
    input[type="email"]:focus {
      border-color: #ef4444;
      outline: none;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    input::placeholder {
      color: #6b7280;
    }
    
    /* Specs grid */
    .spec-item {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid #2d3748;
    }
    
    /* Warning box */
    .warning-box {
      background: rgba(251, 191, 36, 0.1);
      border: 1px solid #f59e0b;
    }
    
    /* Hose calculation */
    .hose-calc {
      background: rgba(59, 130, 246, 0.05);
      border: 1px solid #3b82f6;
    }
    
    /* Mobile adaptations */
    @media (max-width: 640px) {
      .container {
        padding-left: 12px;
        padding-right: 12px;
      }
      
      .industrial-card {
        padding: 16px;
      }
      
      .question-slide {
        padding: 20px;
      }
      
      .question-slide h2 {
        font-size: 18px;
      }
      
      .question-slide h1 {
        font-size: 24px;
      }
      
      .step-indicator {
        width: 32px;
        height: 32px;
        font-size: 10px;
      }
      
      .result-card {
        padding: 16px;
      }
      
      .result-card h3 {
        font-size: 16px;
      }
      
      .result-card .spec-item {
        padding: 8px;
      }
      
      .hose-calc {
        padding: 16px;
      }
      
      .hose-calc h4 {
        font-size: 16px;
      }
      
      /* Header and buttons mobile */
      .industrial-card .flex.items-center.gap-4 {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      
      .industrial-card .w-12.h-12 {
        width: 40px;
        height: 40px;
      }
      
      .industrial-card .w-12.h-12 svg {
        width: 20px;
        height: 20px;
      }
      
      .flex.gap-3 {
        flex-direction: column;
        width: 100%;
      }
      
      .flex.gap-3 button {
        width: 100%;
        justify-content: center;
      }
      
      .radio-option {
        padding: 12px;
      }
      
      .radio-option span {
        font-size: 14px;
      }
      
      #nextBtn, #prevBtn {
        font-size: 14px;
        padding: 12px 20px;
      }
    }
  </style>
</head>
<body>
  
  <div class="grid-bg"></div>
  
  <!-- ============ TELEGRAM CONFIG ============ -->
  <script>
    const TELEGRAM_CONFIG = {
      BOT_TOKEN: 'YOUR_BOT_TOKEN_HERE',
      CHAT_ID: 'YOUR_CHAT_ID_HERE'
    };
  </script>

  <div class="content-wrapper min-h-screen py-8 px-4">
    <div class="container mx-auto max-w-4xl">
      
      <!-- Header -->
      <div class="mb-8">
        <div class="industrial-card rounded-lg p-8">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-700 rounded flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-white mb-1">Подбор дымососа и рукавов</h1>
              <p class="text-gray-400 text-sm">Профессиональный расчёт оборудования под ваше помещение</p>
            </div>
          </div>
          <div class="accent-line"></div>
        </div>
      </div>

      <!-- Progress -->
      <div class="industrial-card rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-3">
          <div class="text-sm text-gray-400">
            Шаг <span class="text-white font-bold text-lg mx-1" id="currentStep">1</span> из <span class="text-white">8</span>
          </div>
          <div class="text-sm text-gray-400">
            Прогресс: <span class="text-red-500 font-semibold" id="progressPercent">12%</span>
          </div>
        </div>
        
        <div class="progress-track h-1.5 rounded-full mb-4">
          <div class="progress-fill h-full rounded-full" id="progressBar" style="width: 12%"></div>
        </div>
        
        <div class="flex gap-2">
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400 active" data-step="1">1</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="2">2</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="3">3</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="4">4</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="5">5</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="6">6</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="7">7</div>
          <div class="step-indicator rounded flex items-center justify-center text-xs font-bold text-gray-400" data-step="8">8</div>
        </div>
      </div>

      <!-- Quiz Form -->
      <form id="smokeFanQuiz">
        
        <!-- Question 1 -->
        <div class="question-slide industrial-card rounded-lg p-8 animate-slide-in" data-question="1">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 1/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Какой площади помещение, из которого нужно удалять дым/аэрозоль?
            </h2>
            <p class="text-gray-400 text-sm">Оценочно</p>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q1" value="to30" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">До 30 м² (небольшая комната, мастерская)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q1" value="30-70" data-score="2" class="hidden" />
              <span class="text-gray-300 font-medium">30–70 м² (типовой цех, гараж, небольшой склад)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q1" value="70-150" data-score="3" class="hidden" />
              <span class="text-gray-300 font-medium">70–150 м² (крупный цех, зал)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q1" value="150plus" data-score="4" class="hidden" />
              <span class="text-gray-300 font-medium">Более 150 м² (большой цех, ангар, склад)</span>
            </label>
          </div>
        </div>

        <!-- Question 2 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="2">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 2/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Насколько важна скорость удаления дымовоздушной среды?
            </h2>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q2" value="normal" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">Обычная, без особых требований по скорости</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q2" value="medium" data-score="2" class="hidden" />
              <span class="text-gray-300 font-medium">Желательно побыстрее стандартного</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q2" value="high" data-score="3" class="hidden" />
              <span class="text-gray-300 font-medium">Высокая скорость важна</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q2" value="max" data-score="4" class="hidden" />
              <span class="text-gray-300 font-medium">Нужна максимальная производительность (крупный объект)</span>
            </label>
          </div>
        </div>

        <!-- Question 3 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="3">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 3/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Есть ли ограничения по габаритам оборудования?
            </h2>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q3" value="very-compact" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">Да, нужно максимально компактное решение</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q3" value="medium" data-score="2" class="hidden" />
              <span class="text-gray-300 font-medium">Средние габариты допустимы</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q3" value="any" data-score="3" class="hidden" />
              <span class="text-gray-300 font-medium">Размеры не критичны</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q3" value="big-ok" data-score="4" class="hidden" />
              <span class="text-gray-300 font-medium">Большие габариты не проблема, главное — мощность</span>
            </label>
          </div>
        </div>

        <!-- Question 4 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="4">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 4/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              На какую производительность ориентируетесь?
            </h2>
            <p class="text-gray-400 text-sm">Если не знаете — выберите по ощущению</p>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q4" value="1500" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">~1500 м³/ч (небольшие помещения)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q4" value="2000-2500" data-score="2" class="hidden" />
              <span class="text-gray-300 font-medium">2000–2500 м³/ч (средние помещения)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q4" value="3500" data-score="3" class="hidden" />
              <span class="text-gray-300 font-medium">~3500 м³/ч (повышенная производительность)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q4" value="8000plus" data-score="4" class="hidden" />
              <span class="text-gray-300 font-medium">От 8000 м³/ч и выше (крупные объекты)</span>
            </label>
          </div>
        </div>

        <!-- Question 5 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="5">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 5/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Какое исполнение корпуса требуется?
            </h2>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q5" value="carbon" data-score="0" class="hidden" />
              <span class="text-gray-300 font-medium">Общего назначения (углеродистая сталь с полимерным покрытием)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q5" value="stainless" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">Коррозионностойкое (нержавеющая сталь 08Х18Н10)</span>
            </label>
          </div>
        </div>

        <!-- Question 6 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="6">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 6/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Какое исполнение электродвигателя планируется?
            </h2>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q6" value="single" data-score="1" class="hidden" />
              <span class="text-gray-300 font-medium">Однофазный (обычная бытовая сеть)</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q6" value="three" data-score="2" class="hidden" />
              <span class="text-gray-300 font-medium">Трёхфазный общепромышленного назначения</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q6" value="special" data-score="3" class="hidden" />
              <span class="text-gray-300 font-medium">Взрывобезопасное или морское исполнение</span>
            </label>
          </div>
        </div>

        <!-- Question 7 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="7">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 7/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Расстояние от зоны забора дыма до места установки дымососа (всасывающая линия, РВ)?
            </h2>
            <p class="text-gray-400 text-sm">Рекомендуемая максимальная длина всасывающей линии без заметной потери мощности — до 10 м</p>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q7" value="to3" class="hidden" />
              <span class="text-gray-300 font-medium">До 3 м — дымосос можно поставить почти рядом с зоной забора</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q7" value="3-5" class="hidden" />
              <span class="text-gray-300 font-medium">3–5 м — оборудование чуть в стороне от зоны дыма</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q7" value="5-10" class="hidden" />
              <span class="text-gray-300 font-medium">5–10 м — дымосос в соседней части помещения</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q7" value="10plus" class="hidden" />
              <span class="text-gray-300 font-medium">Более 10 м — дымосос в другом помещении/далеко</span>
            </label>
          </div>
        </div>

        <!-- Question 8 -->
        <div class="question-slide industrial-card rounded-lg p-8 hidden" data-question="8">
          <div class="mb-6">
            <div class="inline-block px-3 py-1 rounded bg-red-600/20 border border-red-600/30 text-red-500 text-xs font-bold mb-3">
              ВОПРОС 8/8
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">
              Расстояние от дымососа до точки выброса дыма на улицу по напорной линии (РН)?
            </h2>
            <p class="text-gray-400 text-sm">Рекомендуемая максимальная длина напорной линии без потери мощности — до 60 м</p>
          </div>
          
          <div class="space-y-3">
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="to5" class="hidden" />
              <span class="text-gray-300 font-medium">До 5 м — окно/проём рядом</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="5-10" class="hidden" />
              <span class="text-gray-300 font-medium">5–10 м — вывод дыма через ближайшее окно/дверь</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="10-20" class="hidden" />
              <span class="text-gray-300 font-medium">10–20 м — окно/дверь в конце помещения или рядом</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="20-40" class="hidden" />
              <span class="text-gray-300 font-medium">20–40 м — вывод дыма на удалённую сторону здания</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="40-60" class="hidden" />
              <span class="text-gray-300 font-medium">40–60 м — длинная трасса по коридору/галерее</span>
            </label>
            
            <label class="radio-option flex items-center gap-4 p-4 rounded cursor-pointer">
              <div class="radio-dot flex-shrink-0"></div>
              <input type="radio" name="q8" value="60plus" class="hidden" />
              <span class="text-gray-300 font-medium">Более 60 м — точка выброса сильно удалена</span>
            </label>
          </div>
        </div>

        <!-- Navigation -->
        <div class="flex gap-4 mt-6">
          <button type="button" id="prevBtn" class="hidden btn-secondary px-6 py-3 rounded font-semibold text-gray-300 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Назад
          </button>
          
          <button type="button" id="nextBtn" class="flex-1 btn-primary px-6 py-3 rounded font-semibold text-white flex items-center justify-center gap-2">
            <span id="nextBtnText">Следующий вопрос</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- Error -->
        <div id="errorMessage" class="hidden mt-4 industrial-card rounded p-4 border-2 border-red-600/50">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span id="errorText" class="text-red-400 font-medium"></span>
          </div>
        </div>
      </form>

      <!-- Result -->
      <div id="result" class="hidden">
        <div class="industrial-card rounded-lg p-8">
          <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-700 rounded flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Результат подбора</h2>
                <p id="scoreText" class="text-gray-400 text-sm"></p>
              </div>
            </div>
            <div class="accent-line"></div>
          </div>

          <div id="productRecommendation" class="space-y-6 mb-6"></div>
          
          <!-- Hoses calculation -->
          <div id="hosesBlock" class="mt-8">
            <h3 class="text-xl font-bold text-white mb-4">Расчёт рукавов под ваше помещение</h3>
            <div class="space-y-4" id="hosesContent"></div>
          </div>

          <!-- Contact Form in Results -->
          <div class="mt-8 industrial-card rounded-lg p-6">
            <h3 class="text-lg font-bold text-white mb-3">Оставьте контакты для консультации</h3>
            <p class="text-gray-400 text-sm mb-4">Необязательно, но поможет получить детальную информацию и персональное предложение</p>
            <div class="grid md:grid-cols-2 gap-4">
              <input type="text" id="userName" placeholder="Ваше имя" 
                class="w-full px-5 py-3 rounded" />
              <input type="tel" id="userPhone" placeholder="+7 (___) ___-__-__" 
                class="w-full px-5 py-3 rounded" />
              <input type="email" id="userEmail" placeholder="email@example.com" 
                class="w-full px-5 py-3 rounded md:col-span-2" />
            </div>
            <button id="sendContactsBtn" class="mt-4 btn-primary px-6 py-3 rounded font-semibold text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
              Отправить контакты
            </button>
            <div id="contactSuccess" class="hidden mt-3 text-green-400 text-sm">✓ Контакты отправлены! Мы свяжемся с вами в ближайшее время.</div>
          </div>

          <div class="mt-6 pt-6 border-t border-gray-700 flex gap-3">
            <button onclick="window.print()" class="btn-secondary px-6 py-3 rounded font-semibold text-gray-300 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              Печать
            </button>
            <button id="newQuizBtn" class="btn-primary px-6 py-3 rounded font-semibold text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Новый подбор
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    const products = {
      base: [
        {
          id: 1,
          name: "Дымосос ДПЭ-7 (1ЦМ) ДПЭ-А-К-2,0 (1500)",
          performance: "1500 м³/ч",
          size: "310×500×470 мм",
          weight: "15 кг",
          tagline: "Компактный дымосос для небольших помещений",
          description: "Основное назначение: снижение концентрации огнетушащего вещества и температуры дымовоздушной среды. Дополнительно подходит для нормализации воздушной среды при сварочных работах. Доступен в обычном и коррозионностойком исполнении, с однофазным и трёхфазным, в том числе взрывобезопасным и морским электродвигателем."
        },
        {
          id: 2,
          name: "Дымосос ДПЭ-7 (2ЦМ) ДПЭ-А-К-2,0 (2000)",
          performance: "от 2000 м³/ч",
          size: "380×500×570 мм",
          weight: "28 кг",
          tagline: "Повышенная производительность для типовых задач",
          description: "Подходит для помещений средних размеров. Обеспечивает эффективное снижение концентрации огнетушащего вещества и дымовоздушной среды после пожара и при сварочных работах. Есть варианты общего и коррозионностойкого исполнения, а также разные исполнения двигателя."
        },
        {
          id: 3,
          name: "Дымосос ДПЭ-7 (2,5ЦМ) ДПЭ-А-К-2,5 (2000)",
          performance: "2500 м³/ч",
          size: "465×550×570 мм",
          weight: "30 кг",
          tagline: "Оптимальный баланс расхода и габаритов",
          description: "Рекомендуется для производственных помещений и складов, где важна чуть более высокая производительность по сравнению с моделями на 2000 м³/ч. Доступен в обычном и коррозионностойком исполнении, с различными вариантами электродвигателей."
        },
        {
          id: 4,
          name: "Дымосос ДПЭ-7 (4ЦМ)",
          performance: "от 3500 м³/ч",
          size: "380×500×570 мм",
          weight: "30 кг",
          tagline: "Мощный при компактных габаритах",
          description: "Подходит для объектов, где требуется повышенная производительность и при этом важны умеренные габариты. Может использоваться для постпожарной вентиляции и снижения концентрации продуктов горения."
        },
        {
          id: 5,
          name: "Дымосос ДПЭ-А-К-2,5 (3500)",
          performance: "3500 м³/ч",
          size: "465×550×570 мм",
          weight: "30 кг",
          tagline: "Гибкое исполнение под разные условия эксплуатации",
          description: "Обеспечивает производительность 3500 м³/ч и может комплектоваться однофазными, трёхфазными, взрывобезопасными и морскими электродвигателями. Подходит для сложных объектов с особыми требованиями к оборудованию."
        },
        {
          id: 6,
          name: "Дымосос ДПЭ-7 (4ОТМ)",
          performance: "от 8000 м³/ч",
          size: "550×550×650 мм",
          weight: "46 кг",
          tagline: "Высокопроизводительный дымосос для крупных помещений",
          description: "Предназначен для снижения концентрации огнетушащего вещества и температуры дымовоздушной среды, а также нормализации воздуха после срабатывания системы пожаротушения в крупных помещениях."
        },
        {
          id: 7,
          name: "Дымосос ДПЭ-7 (5ОТМ)",
          performance: "от 12000 м³/ч",
          size: "600×600×820 мм",
          weight: "59 кг",
          tagline: "Очень высокая производительность для больших площадей",
          description: "Используется на крупных складах, цехах и логистических объектах, где требуется максимальная скорость удаления дымовоздушной смеси."
        },
        {
          id: 8,
          name: "Дымосос ДПЭ-7 (6ОТМ)",
          performance: "от 15000 м³/ч",
          size: "600×720×830 мм",
          weight: "63 кг",
          tagline: "Максимальная производительность в линейке",
          description: "Оптимален для очень крупных объектов, где важна максимально быстрая нормализация воздушной среды после пожара или работы систем пожаротушения."
        }
      ],
      hoses: {
        rv: {
          name: "Рукав всасывающий дополнительный РВ",
          baseLength: 5,
          description: "Длина одного рукава — 5 м. Подходит для всех стандартных дымососов. Рекомендуемая максимальная длина всасывающей линии без заметной потери мощности — до 10 м."
        },
        rn: {
          name: "Рукав напорный дополнительный РН",
          baseLength: 10,
          description: "Длина одного рукава — 10 м. Подходит для всех стандартных дымососов. Рекомендуемая максимальная длина напорной линии без заметной потери мощности — до 60 м."
        }
      }
    };

    let currentStep = 1;
    const totalSteps = 8;
    const answers = {};
    let contactFormShown = false;

    // Radio selection
    document.querySelectorAll('.radio-option').forEach(option => {
      option.addEventListener('click', function() {
        const input = this.querySelector('input[type="radio"]');
        const questionName = input.name;
        
        document.querySelectorAll(`input[name="${questionName}"]`).forEach(radio => {
          radio.closest('.radio-option').classList.remove('selected');
        });
        
        input.checked = true;
        this.classList.add('selected');
      });
    });

    function showStep(step) {
      document.querySelectorAll('.question-slide').forEach(slide => {
        slide.classList.add('hidden');
      });
      
      const currentSlide = document.querySelector(`.question-slide[data-question="${step}"]`);
      if (currentSlide) {
        currentSlide.classList.remove('hidden');
        currentSlide.classList.add('animate-slide-in');
      }
      
      document.getElementById('currentStep').textContent = step;
      const percent = Math.round((step / totalSteps) * 100);
      document.getElementById('progressPercent').textContent = percent + '%';
      document.getElementById('progressBar').style.width = percent + '%';
      
      document.querySelectorAll('.step-indicator').forEach((dot, index) => {
        const dotStep = index + 1;
        dot.classList.remove('active', 'completed');
        dot.style.color = '#6b7280';
        if (dotStep === step) {
          dot.classList.add('active');
          dot.style.color = '#ef4444';
        } else if (dotStep < step) {
          dot.classList.add('completed');
          dot.style.color = '#10b981';
        }
      });
      
      const prevBtn = document.getElementById('prevBtn');
      const nextBtnText = document.getElementById('nextBtnText');
      
      if (step === 1) {
        prevBtn.classList.add('hidden');
      } else {
        prevBtn.classList.remove('hidden');
      }
      
      if (step === totalSteps) {
        nextBtnText.textContent = 'Получить результат';
      } else {
        nextBtnText.textContent = 'Следующий вопрос';
      }
    }

    document.getElementById('nextBtn').addEventListener('click', function() {
      const errorMessage = document.getElementById('errorMessage');
      const errorText = document.getElementById('errorText');
      
      errorMessage.classList.add('hidden');
      
      if (currentStep <= totalSteps) {
        const currentQuestion = document.querySelector(`.question-slide[data-question="${currentStep}"]`);
        const selected = currentQuestion.querySelector('input[type="radio"]:checked');
        
        if (!selected) {
          errorText.textContent = 'Пожалуйста, выберите один из вариантов';
          errorMessage.classList.remove('hidden');
          return;
        }
        
        answers[`q${currentStep}`] = {
          value: selected.value,
          score: Number(selected.dataset.score || 0)
        };
      }
      
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        submitQuiz();
      }
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
      if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });

    function getRecommendationByScore(score) {
      if (score <= 5) return { main: products.base[0], alternatives: [] };
      else if (score <= 10) return { main: products.base[1], alternatives: [products.base[2]] };
      else if (score <= 14) return { main: products.base[3], alternatives: [products.base[4]] };
      else if (score <= 18) return { main: products.base[5], alternatives: [] };
      else if (score <= 22) return { main: products.base[6], alternatives: [] };
      else return { main: products.base[7], alternatives: [] };
    }

    function renderProductCard(product, isMain = false) {
      const badgeClass = isMain ? 'badge' : 'badge alt';
      const cardClass = isMain ? 'main' : '';

      return `
        <div class="result-card rounded-lg p-6 ${cardClass}">
          <div class="inline-block px-3 py-1 rounded ${badgeClass} text-xs font-bold mb-3">
            ${isMain ? 'РЕКОМЕНДУЕТСЯ' : 'АЛЬТЕРНАТИВА'}
          </div>
          <h3 class="text-xl font-bold text-white mb-2">${product.name}</h3>
          <p class="text-gray-400 text-sm mb-4">${product.tagline}</p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            <div class="spec-item rounded p-3">
              <div class="text-xs text-gray-500 mb-1">Производительность</div>
              <div class="font-bold text-white text-sm">${product.performance}</div>
            </div>
            <div class="spec-item rounded p-3">
              <div class="text-xs text-gray-500 mb-1">Габариты</div>
              <div class="font-bold text-white text-xs">${product.size}</div>
            </div>
            <div class="spec-item rounded p-3">
              <div class="text-xs text-gray-500 mb-1">Масса</div>
              <div class="font-bold text-white text-sm">${product.weight}</div>
            </div>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed">${product.description}</p>
        </div>
      `;
    }

    function calculateSuctionHose(q7Value) {
      const base = products.hoses.rv.baseLength;
      let approxDistance = 0;
      let comment = "";
      let warning = "";
      
      switch (q7Value) {
        case "to3":
          approxDistance = 3;
          comment = "Дымосос можно установить рядом с зоной забора дыма — потери давления будут минимальны.";
          break;
        case "3-5":
          approxDistance = 5;
          comment = "Дымосос располагается на небольшом удалении от зоны забора дыма. Это комфортный режим для оборудования.";
          break;
        case "5-10":
          approxDistance = 10;
          comment = "Дымосос располагается в другой части помещения — это верхняя граница рекомендуемой длины всасывающей линии.";
          break;
        case "10plus":
          approxDistance = 12;
          comment = "Дымосос планируется установить достаточно далеко от зоны забора дыма.";
          warning = "Рекомендуется по возможности сократить длину всасывающей линии до 10 м, чтобы избежать потери мощности.";
          break;
      }
      
      const requiredLength = Math.ceil(approxDistance / base) * base;
      const hoseCount = requiredLength / base;
      
      return {
        approxDistance,
        requiredLength,
        hoseCount,
        comment,
        warning: requiredLength > 10 ? warning || "Длина всасывающей линии более 10 м может приводить к заметной потере мощности дымососа." : warning
      };
    }

    function calculateDischargeHose(q8Value) {
      const base = products.hoses.rn.baseLength;
      let approxDistance = 0;
      let comment = "";
      let warning = "";
      let valid = true;
      
      switch (q8Value) {
        case "to5":
          approxDistance = 5;
          comment = "Окно или проём для выброса дыма расположены рядом с дымососом.";
          break;
        case "5-10":
          approxDistance = 10;
          comment = "Окно или дверь для выброса дыма находятся в пределах одного помещения.";
          break;
        case "10-20":
          approxDistance = 20;
          comment = "Дым выводится через более удалённое окно или дверь, возможно, в конце помещения.";
          break;
        case "20-40":
          approxDistance = 40;
          comment = "Трасса напорной линии проходит по коридору или через часть здания.";
          break;
        case "40-60":
          approxDistance = 60;
          comment = "Используется максимально допустимая длина напорной линии без серьёзной потери мощности.";
          break;
        case "60plus":
          approxDistance = 70;
          comment = "Точка выброса дыма сильно удалена от места установки дымососа.";
          warning = "Рекомендуемая максимальная длина напорной линии — до 60 м. При большей длине возможна существенная потеря давления и эффективности.";
          valid = false;
          break;
      }
      
      if (!valid) {
        return {
          approxDistance,
          requiredLength: null,
          hoseCount: null,
          comment,
          warning,
          valid: false
        };
      }
      
      const requiredLength = Math.ceil(approxDistance / base) * base;
      const hoseCount = requiredLength / base;
      
      return {
        approxDistance,
        requiredLength,
        hoseCount,
        comment,
        warning,
        valid: true
      };
    }

    async function sendToTelegram(data) {
      if (!TELEGRAM_CONFIG.BOT_TOKEN || !TELEGRAM_CONFIG.CHAT_ID || 
          TELEGRAM_CONFIG.BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE' ||
          TELEGRAM_CONFIG.CHAT_ID === 'YOUR_CHAT_ID_HERE') {
        return false;
      }

      const message = `
🎯 <b>Новая заявка из квиза дымососов</b>

📊 <b>Результат:</b>
• Баллов: ${data.score}
• Рекомендация: ${data.recommendation}

📋 <b>Ответы:</b>
${data.answers}

🔧 <b>Расчёт рукавов:</b>
${data.hoses}

${data.contact ? `👤 <b>Контакты:</b>\n${data.contact}` : '📧 <i>Контакты не указаны</i>'}

📅 ${new Date().toLocaleString('ru-RU')}
      `.trim();

      try {
        const response = await fetch(`https://api.telegram.org/bot${TELEGRAM_CONFIG.BOT_TOKEN}/sendMessage`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            chat_id: TELEGRAM_CONFIG.CHAT_ID,
            text: message,
            parse_mode: 'HTML'
          })
        });
        return response.ok;
      } catch (error) {
        console.error('Telegram error:', error);
        return false;
      }
    }

    async function submitQuiz() {
      let totalScore = 0;
      const answerTexts = [];
      
      for (let i = 1; i <= totalSteps; i++) {
        const answer = answers[`q${i}`];
        totalScore += answer.score;
        
        const questionText = document.querySelector(`.question-slide[data-question="${i}"] h2`).textContent;
        const selectedLabel = document.querySelector(`input[name="q${i}"][value="${answer.value}"]`)
          .closest('.radio-option').querySelector('span').textContent;
        answerTexts.push(`${questionText}\n→ ${selectedLabel}`);
      }
      
      const recommendation = getRecommendationByScore(totalScore);
      
      // Calculate hoses
      const suction = calculateSuctionHose(answers.q7.value);
      const discharge = calculateDischargeHose(answers.q8.value);
      
      const hosesText = `
РВ: ~${suction.approxDistance}м → ${suction.requiredLength}м (${suction.hoseCount} шт по 5м)
РН: ~${discharge.approxDistance}м → ${discharge.valid ? `${discharge.requiredLength}м (${discharge.hoseCount} шт по 10м)` : 'Превышен лимит 60м'}`;
      
      // Send to Telegram without contacts
      await sendToTelegram({
        score: totalScore,
        recommendation: recommendation.main.name,
        answers: answerTexts.join('\n\n'),
        hoses: hosesText,
        contact: ''
      });
      
      document.getElementById('smokeFanQuiz').classList.add('hidden');
      document.querySelector('.industrial-card.rounded-lg.p-6').classList.add('hidden');
      
      document.getElementById('scoreText').textContent = `Суммарный результат: ${totalScore} баллов`;
      
      let html = renderProductCard(recommendation.main, true);
      if (recommendation.alternatives.length > 0) {
        recommendation.alternatives.forEach(alt => {
          html += renderProductCard(alt, false);
        });
      }
      document.getElementById('productRecommendation').innerHTML = html;
      
      // Render hoses
      let hosesHtml = '';
      
      // Suction hose
      hosesHtml += `
        <div class="hose-calc rounded-lg p-6">
          <h4 class="text-lg font-bold text-white mb-3">
            <svg class="w-5 h-5 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
            ${products.hoses.rv.name}
          </h4>
          <div class="text-sm text-gray-300 space-y-2">
            <p><strong class="text-white">Оценочное расстояние:</strong> ~${suction.approxDistance} м</p>
            <p><strong class="text-white">Рекомендуемая длина:</strong> ${suction.requiredLength} м</p>
            <p><strong class="text-white">Количество рукавов:</strong> ${suction.hoseCount} шт. по ${products.hoses.rv.baseLength} м</p>
            <p class="text-gray-400 italic">${suction.comment}</p>
          </div>
          ${suction.warning ? `<div class="warning-box rounded p-3 mt-3 text-sm text-yellow-400">${suction.warning}</div>` : ''}
        </div>
      `;
      
      // Discharge hose
      if (discharge.valid) {
        hosesHtml += `
          <div class="hose-calc rounded-lg p-6">
            <h4 class="text-lg font-bold text-white mb-3">
              <svg class="w-5 h-5 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
              ${products.hoses.rn.name}
            </h4>
            <div class="text-sm text-gray-300 space-y-2">
              <p><strong class="text-white">Оценочное расстояние:</strong> ~${discharge.approxDistance} м</p>
              <p><strong class="text-white">Рекомендуемая длина:</strong> ${discharge.requiredLength} м</p>
              <p><strong class="text-white">Количество рукавов:</strong> ${discharge.hoseCount} шт. по ${products.hoses.rn.baseLength} м</p>
              <p class="text-gray-400 italic">${discharge.comment}</p>
            </div>
            ${discharge.warning ? `<div class="warning-box rounded p-3 mt-3 text-sm text-yellow-400">${discharge.warning}</div>` : ''}
          </div>
        `;
      } else {
        hosesHtml += `
          <div class="hose-calc rounded-lg p-6">
            <h4 class="text-lg font-bold text-white mb-3">
              <svg class="w-5 h-5 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
              ${products.hoses.rn.name}
            </h4>
            <div class="text-sm text-gray-300 space-y-2">
              <p><strong class="text-white">Оценочное расстояние:</strong> ~${discharge.approxDistance} м</p>
              <p class="text-red-400"><strong>Нельзя корректно подобрать напорную линию:</strong> рекомендуемая максимальная длина — до 60 м</p>
              <p class="text-gray-400 italic">${discharge.comment}</p>
            </div>
            <div class="warning-box rounded p-3 mt-3 text-sm text-yellow-400">${discharge.warning}</div>
          </div>
        `;
      }
      
      hosesHtml += `
        <div class="text-sm text-gray-400 mt-4 p-4 industrial-card rounded-lg">
          <p>Обратите внимание: дополнительные рукава можно соединять между собой с помощью соединительных колец с муфтами. Диаметр рукавов подбирается в соответствии с выбранной моделью дымососа.</p>
        </div>
      `;
      
      document.getElementById('hosesContent').innerHTML = hosesHtml;
      
      document.getElementById('result').classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.getElementById('newQuizBtn').addEventListener('click', function() {
      currentStep = 1;
      Object.keys(answers).forEach(key => delete answers[key]);
      
      document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
        radio.closest('.radio-option').classList.remove('selected');
      });
      
      document.getElementById('userName').value = '';
      document.getElementById('userPhone').value = '';
      document.getElementById('userEmail').value = '';
      document.getElementById('contactSuccess').classList.add('hidden');
      
      document.getElementById('result').classList.add('hidden');
      document.getElementById('smokeFanQuiz').classList.remove('hidden');
      document.querySelector('.industrial-card.rounded-lg.p-6').classList.remove('hidden');
      
      showStep(1);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Send contacts button handler
    document.addEventListener('click', function(e) {
      if (e.target.id === 'sendContactsBtn' || e.target.closest('#sendContactsBtn')) {
        const userName = document.getElementById('userName').value.trim();
        const userPhone = document.getElementById('userPhone').value.trim();
        const userEmail = document.getElementById('userEmail').value.trim();
        
        if (!userName && !userPhone && !userEmail) {
          alert('Пожалуйста, заполните хотя бы одно поле');
          return;
        }
        
        let contactInfo = [
          userName ? `• Имя: ${userName}` : '',
          userPhone ? `• Телефон: ${userPhone}` : '',
          userEmail ? `• Email: ${userEmail}` : ''
        ].filter(Boolean).join('\n');
        
        // Send contacts to Telegram
        if (TELEGRAM_CONFIG.BOT_TOKEN && TELEGRAM_CONFIG.CHAT_ID && 
            TELEGRAM_CONFIG.BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE' &&
            TELEGRAM_CONFIG.CHAT_ID !== 'YOUR_CHAT_ID_HERE') {
          
          const message = `
📞 <b>Запрос на консультацию</b>

${contactInfo}

📅 ${new Date().toLocaleString('ru-RU')}
          `.trim();
          
          fetch(`https://api.telegram.org/bot${TELEGRAM_CONFIG.BOT_TOKEN}/sendMessage`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              chat_id: TELEGRAM_CONFIG.CHAT_ID,
              text: message,
              parse_mode: 'HTML'
            })
          }).then(() => {
            document.getElementById('contactSuccess').classList.remove('hidden');
            document.getElementById('sendContactsBtn').disabled = true;
            document.getElementById('sendContactsBtn').style.opacity = '0.5';
          });
        } else {
          document.getElementById('contactSuccess').classList.remove('hidden');
          document.getElementById('sendContactsBtn').disabled = true;
          document.getElementById('sendContactsBtn').style.opacity = '0.5';
        }
      }
    });

    showStep(1);
  </script>
</body>
</html>