@extends('layouts.admin')

@section('content')
<style>
  /* Container bọc ngoài */
  .game-container {
    background: #f1f1f1;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    text-align: center;
    max-width: 500px;
  }

  .board-wrapper {
    position: relative; /* thêm để ::after bám vào */
    overflow: hidden;
    border: 2px solid rgba(220,220,255,0.8);
    box-shadow: 0 0 30px rgba(180, 200, 255, 0.8),
                0 0 60px rgba(200, 220, 255, 0.5) inset;
    background: linear-gradient(135deg, #f8f9ff, #e6f0ff, #d9d9f2, #cccccc);
    padding: 5px;
  }

  /* lớp mây nhanh */
  .board-wrapper::after {
    content: "";
    position: absolute;
    inset: -50%;
    background: url("{{ asset('images/textture_may.png') }}") repeat;
    background-size: 400px 400px;
    animation: clouds 20s linear infinite;
    opacity: 0.6;
    mix-blend-mode: screen;
    z-index: 1;
  }

  /* lớp mây chậm */
  .board-wrapper::before {
    content: "";
    position: absolute;
    inset: -50%;
    background: url("{{ asset('images/textture_may.png') }}") repeat;
    background-size: 600px 600px;
    animation: clouds-slow 60s linear infinite;
    opacity: 0.3;
    mix-blend-mode: screen;
    z-index: 1;
  }

  @keyframes clouds-slow {
    from { background-position: 0 0; }
    to   { background-position: 1200px 600px; }
  }

  @keyframes clouds {
    from { background-position: 0 0; }
    to   { background-position: 800px 400px; }
  }

  canvas {
      border: 3px solid #aaa;
      position: relative;
      z-index: 2; /* để nổi trên mây */
      border-radius: 3px;
    }

  /* Nhóm nút chung */
  .button-group {
    margin-bottom: 15px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }

  /* Nút icon */
  .button-group button {
    border: none;
    border-radius: 8px;
    background-color: #f8f9fa;
    cursor: pointer;
    padding: 5px;
    transition: background-color 0.3s, transform 0.1s;
  }

  .button-group button:hover {
    background-color: #e2e6ea;
  }

  .button-group button:active {
    transform: scale(0.95);
  }

  .button-group button img {
    width: 24px;
    height: 24px;
  }

  /* Nút Computer */
  .computer-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
  }

  #btnComputerGreen.active-green {
    background-color: #28a745;
  }

  #btnComputerRed.active-red {
    background-color: #dc3545;
  }

  .computer-btn.inactive {
    background-color: #9e9e9e !important;
  }

  .variation-btn {
    width: 150px;
    border: 1px solid #123 !important;
  }

  #variations-container {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 5px;
  }

  .variation-btn {
      padding: 6px 10px;
      border: 1px solid #ccc;
      background: #f7f7f7;
      cursor: pointer;
      border-radius: 4px;
  }
  .variation-btn:hover {
      background: #e0e0e0;
  }

</style>
<div class="text-center button-group">
    
    <div class="game-container mb-5 pt-3">
        <div class="button-group">
            <label for="openingSelect" class="form-label d-block">Vui lòng chọn 1 thế trận để luyện tập</label>
            <select id="openingSelect" class="form-select" style="max-width: 300px;">
                <optgroup label="Đỏ">
                    @foreach(config('openings.red') as $opening)
                        <option value="{{ $opening['id'] }}" data-color="red">{{ $opening['name'] }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Xanh">
                    @foreach(config('openings.green') as $opening)
                        <option value="{{ $opening['id'] }}" data-color="green">{{ $opening['name'] }}</option>
                    @endforeach
                </optgroup>
            </select>

            
        </div>
        <div class="board-wrapper">
          <canvas id="chessboard" width="355" height="420"></canvas>
        </div>

        <div class="button-group mt-4">
            <button id="newButton"><img src="{{ asset('images/new.png') }}" alt="New"></button>
            <button id="backButton"><img src="{{ asset('images/back.png') }}" alt="Back"></button>
            <button id="rotateButton"><img src="{{ asset('images/xoay.jpg') }}" alt="Rotate"></button>
            <button onclick='saveBook()'><img src="{{ asset('images/save.png') }}" alt="Save"></button>
            <button onclick='hiddenBook()'><img src="{{ asset('images/delete.png') }}" alt="Delete"></button>
        </div>

        <div id="result" style="min-height:100px; text-align: left;" class="card p-2 mb-3 mt-2 mx-3"></div>

        <div class="my-3 text-center" style="display:none;">
            <textarea id="comment" class="form-control" rows="3" style="max-width: 400px; margin: 0 auto;"></textarea>
        </div>
    </div>
</div>

<script src="{{ asset('js/book.js') }}?t={{ time() }}"></script>
<script src="{{ asset('js/computerAI.js') }}?t={{ time() }}"></script>
<script src="{{ asset('js/script.js') }}?t={{ time() }}"></script>

@endsection
