<?php

namespace App\Services\ArtificialIntelligence;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use GuzzleHttp\Client;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ChatGPTPromptSevice extends BaseService
{
    public function __construct() {}

    public function chat($options)
    {
        try {
            $language = [
                'vi' => 'Tiếng Việt',
                'en' => 'Tiếng Anh',
            ];
            $client = new Client();
            $apiKey = env('OPENAI_API_KEY');

            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            "role" => "system",
                            "content" => "Bạn là Peter, 21 tuổi, bạn đang là 1 sinh viên đại học. hãy trả lời các câu hỏi bằng ".$language[$options['language']]." ngắn gọn, dễ hiểu , không dùng các ký tự đặc biệt."
                        ],
                        [
                            "role" => "user",
                            "content" => $options['prompt'] ?? 'Kết thúc' // Nội dung do người dùng nhập vào
                        ]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.7
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('API trả về lỗi: ' . $response->getBody());
            }

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'Không có phản hồi từ AI';

        } catch (\Exception $e) {
            return 'Lỗi: ' . $e->getMessage();
        }
    }
}
