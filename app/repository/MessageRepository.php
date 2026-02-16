<?php

class MessageRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function send(int $senderId, int $receiverId, string $body): int
	{
		$st = $this->pdo->prepare(
			"INSERT INTO chat(message, sender_id, receiver_id, status) VALUES(?,?,?, 'sent')"
		);
		$st->execute([$body, $senderId, $receiverId]);
		return (int)$this->pdo->lastInsertId();
	}

	public function markDeliveredForUser(int $userId): int
	{
		$st = $this->pdo->prepare("UPDATE chat SET status='delivered' WHERE receiver_id = ? AND status = 'sent'");
		$st->execute([$userId]);
		return $st->rowCount();
	}

	public function markReadFromUser(int $receiverId, int $senderId): int
	{
		$st = $this->pdo->prepare("UPDATE chat SET status='read' WHERE receiver_id = ? AND sender_id = ? AND status <> 'read'");
		$st->execute([$receiverId, $senderId]);
		return $st->rowCount();
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function listBetweenUsers(int $userId, int $otherUserId, int $limit = 200): array
	{
		$limit = max(1, min(500, (int)$limit));
		$sql = "
			SELECT id, sender_id, receiver_id, message, timestamp, status
			FROM chat
			WHERE (sender_id = ? AND receiver_id = ?)
			   OR (sender_id = ? AND receiver_id = ?)
			ORDER BY timestamp ASC, id ASC
			LIMIT $limit
		";
		$st = $this->pdo->prepare($sql);
		$st->execute([$userId, $otherUserId, $otherUserId, $userId]);
		return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function listConversationUsers(int $userId): array
	{
		$sql = "
			SELECT
				u.id,
				u.nom,
				u.prenom,
				u.email,
				(
					SELECT c2.message
					FROM chat c2
					WHERE ((c2.sender_id = ? AND c2.receiver_id = u.id) OR (c2.sender_id = u.id AND c2.receiver_id = ?))
					ORDER BY c2.timestamp DESC, c2.id DESC
					LIMIT 1
				) AS last_message,
				(
					SELECT c3.timestamp
					FROM chat c3
					WHERE ((c3.sender_id = ? AND c3.receiver_id = u.id) OR (c3.sender_id = u.id AND c3.receiver_id = ?))
					ORDER BY c3.timestamp DESC, c3.id DESC
					LIMIT 1
				) AS last_message_time
			FROM users u
			WHERE u.id <> ?
			AND EXISTS (
				SELECT 1
				FROM chat c
				WHERE (c.sender_id = ? AND c.receiver_id = u.id)
				   OR (c.sender_id = u.id AND c.receiver_id = ?)
			)
			ORDER BY last_message_time DESC
		";

		$st = $this->pdo->prepare($sql);
		$st->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId]);
		return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
	}
}
