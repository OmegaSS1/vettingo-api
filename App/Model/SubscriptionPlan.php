<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class SubscriptionPlan implements JsonSerializable
{
	private int $id;
	private string $name;
	private string $slug;
	private ?string $description;
	private ?int $price_monthly;
	private ?int $price_yearly;
	private ?string $features;
	private ?int $max_listings;
	private ?int $max_photos;
	private bool $highlight_listings;
	private bool $priority_support;
	private bool $analytics_access;
	private ?string $stripe_price_id_monthly;
	private ?string $stripe_price_id_yearly;
	private bool $is_active;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, string $name, string $slug, ?string $description, ?int $price_monthly, ?int $price_yearly, ?string $features, ?int $max_listings, ?int $max_photos, bool $highlight_listings, bool $priority_support, bool $analytics_access, ?string $stripe_price_id_monthly, ?string $stripe_price_id_yearly, bool $is_active, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->name = $name;$this->slug = $slug;$this->description = $description;$this->price_monthly = $price_monthly;$this->price_yearly = $price_yearly;$this->features = $features;$this->max_listings = $max_listings;$this->max_photos = $max_photos;$this->highlight_listings = $highlight_listings;$this->priority_support = $priority_support;$this->analytics_access = $analytics_access;$this->stripe_price_id_monthly = $stripe_price_id_monthly;$this->stripe_price_id_yearly = $stripe_price_id_yearly;$this->is_active = $is_active;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getName(): string {return $this->name;}

	public function getSlug(): string {return $this->slug;}

	public function getDescription(): ?string {return $this->description;}

	public function getPriceMonthly(): ?int {return $this->price_monthly;}

	public function getPriceYearly(): ?int {return $this->price_yearly;}

	public function getFeatures(): ?string {return $this->features;}

	public function getMaxListings(): ?int {return $this->max_listings;}

	public function getMaxPhotos(): ?int {return $this->max_photos;}

	public function getHighlightListings(): bool {return $this->highlight_listings;}

	public function getPrioritySupport(): bool {return $this->priority_support;}

	public function getAnalyticsAccess(): bool {return $this->analytics_access;}

	public function getStripePriceIdMonthly(): ?string {return $this->stripe_price_id_monthly;}

	public function getStripePriceIdYearly(): ?string {return $this->stripe_price_id_yearly;}

	public function getIsActive(): bool {return $this->is_active;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"name" => $this->name,
			"slug" => $this->slug,
			"description" => $this->description,
			"priceMonthly" => $this->price_monthly,
			"priceYearly" => $this->price_yearly,
			"features" => $this->features,
			"maxListings" => $this->max_listings,
			"maxPhotos" => $this->max_photos,
			"highlightListings" => $this->highlight_listings,
			"prioritySupport" => $this->priority_support,
			"analyticsAccess" => $this->analytics_access,
			"stripePriceIdMonthly" => $this->stripe_price_id_monthly,
			"stripePriceIdYearly" => $this->stripe_price_id_yearly,
			"isActive" => $this->is_active,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at
        ];
    }
}