<?php

class MessageApiController
{
	private static function requireAuth(): int
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		$userId = (int)($_SESSION['user_id'] ?? 0);
		if ($userId <= 0) {
			Flight::json([
				'ok' => false,
				'error' => 'Not authenticated'
			], 401);
			exit;
		}

		return $userId;
	}

	private static function repo(): MessageRepository
	{
		return new MessageRepository(Flight::db());
	}

	public static function conversations()
	{
		$userId = self::requireAuth();

		try {
			$rows = self::repo()->listConversationUsers($userId);
			$conversations = array_map(function ($row) {
				$name = trim((string)($row['nom'] ?? '') . ' ' . (string)($row['prenom'] ?? ''));
				if ($name === '') {
					$name = (string)($row['email'] ?? 'User');
				}
				$time = (string)($row['last_message_time'] ?? '');
				$displayTime = $time !== '' ? date('H:i', strtotime($time)) : '';

				return [
					'id' => (int)($row['id'] ?? 0),
					'name' => $name,
					'avatar' => '/assets/images/avatar-placeholder.svg',
					'type' => 'User',
					'online' => false,
					'lastMessage' => (string)($row['last_message'] ?? ''),
					'lastMessageTime' => $displayTime,
					'lastSeen' => '',
					'unread' => 0,
				];
			}, $rows);

			Flight::json([
				'ok' => true,
				'conversations' => array_values($conversations),
			]);
		} catch (Throwable $e) {
			Flight::json([
				'ok' => false,
				'error' => 'Failed to load conversations',
			], 500);
		}
	}

	public static function send()
	{
		$userId = self::requireAuth();

		$req = Flight::request();

		$receiverIdRaw = $req->data->receiver_id ?? ($req->data->conversation_id ?? 0);
		$receiverId = (int)$receiverIdRaw;
		$text = trim((string)($req->data->text ?? ''));
		if (strlen($text) > 500) {
			$text = substr($text, 0, 500);
		}

		if ($receiverId <= 0 || $receiverId === $userId || $text === '') {
			Flight::json([
				'ok' => false,
				'error' => 'Missing receiver_id or text'
			], 400);
			return;
		}

		try {
			$id = self::repo()->send($userId, $receiverId, $text);
			Flight::json([
				'ok' => true,
				'message' => [
					'id' => $id,
					'text' => $text,
					'time' => date('H:i'),
					'sent' => true,
				]
			]);
		} catch (Throwable $e) {
			Flight::json([
				'ok' => false,
				'error' => 'Failed to send message',
			], 500);
		}
	}

	public static function list($conversationId)
	{
		$userId = self::requireAuth();
		$otherUserId = (int)$conversationId;
		if ($otherUserId <= 0 || $otherUserId === $userId) {
			Flight::json([
				'ok' => false,
				'error' => 'Invalid conversation id'
			], 400);
			return;
		}

		try {
			// Mark messages from the other user to me as "read" when I open the conversation.
			self::repo()->markReadFromUser($userId, $otherUserId);

			$rows = self::repo()->listBetweenUsers($userId, $otherUserId);
			$messages = array_map(function ($row) use ($userId) {
				$createdAt = (string)($row['timestamp'] ?? '');
				$time = $createdAt !== '' ? date('H:i', strtotime($createdAt)) : '';
				$status = (string)($row['status'] ?? 'sent');
				$isSentByMe = (int)($row['sender_id'] ?? 0) === $userId;
				return [
					'id' => (int)($row['id'] ?? 0),
					'text' => (string)($row['message'] ?? ''),
					'time' => $time,
					'sent' => $isSentByMe,
					'status' => $status,
					'delivered' => $isSentByMe && ($status === 'delivered' || $status === 'read'),
					'read' => $isSentByMe && $status === 'read',
				];
			}, $rows);

			Flight::json([
				'ok' => true,
				'messages' => array_values($messages),
			]);
		} catch (Throwable $e) {
			Flight::json([
				'ok' => false,
				'error' => 'Failed to load messages',
			], 500);
		}
	}
}
